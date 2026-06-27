<?php

namespace Tests\Feature;

use App\Enums\CheckoutStatus;
use App\Mail\ContactMail;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\PaymentSettingsService;
use App\Services\UrlMetadataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Laravel\Cashier\Invoice as CashierInvoice;
use Mockery;
use Mockery\MockInterface;
use Stripe\Customer as StripeCustomer;
use Stripe\Invoice as StripeInvoice;
use Tests\TestCase;

class FrontendControllersTest extends TestCase
{
    use ControllerTestHelpers;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
        $this->withoutMiddleware(\App\Http\Middleware\VerifyPaymentEnabled::class);

        Mail::fake();

        $this->mock(UrlMetadataService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('parse')->andReturn([])->byDefault();
        });
    }

    public function test_public_front_controller_methods(): void
    {
        $owner = $this->user();
        $page = $this->page(['slug' => 'about']);
        $redirectLink = $this->link(null, [
            'alias' => 'public-alias',
            'url' => 'https://example.com/target',
            'clicks' => 1,
        ]);
        $passwordLink = $this->link($owner, [
            'password' => Hash::make('secret'),
            'clicks' => 1,
        ]);
        $passwordLink->forceFill(['alias' => (string) $passwordLink->id])->save();

        $this->get(route('home'))->assertOk()->assertViewIs('home.index');

        $this->from(route('home'))
            ->post(route('guest'), ['url' => 'https://example.com/guest'])
            ->assertRedirect(route('home'));
        $this->assertDatabaseHas('links', ['url' => 'https://example.com/guest', 'user_id' => null]);

        $this->get(route('contact'))->assertOk()->assertViewIs('contact.index');
        $this->from(route('contact'))->post('/contact', [
            'email' => 'sender@example.test',
            'subject' => 'Question',
            'message' => 'Hello from a feature test.',
        ])->assertRedirect(route('contact'));
        Mail::assertSent(ContactMail::class);

        $this->get(route('page', $page->slug))->assertOk()->assertViewIs('page.page');
        $this->get(route('developers'))->assertOk()->assertViewIs('developers.index');
        $this->get(route('qr', $redirectLink->id))->assertOk()->assertViewIs('qr.content');

        $this->get(route('link.preview', $redirectLink->alias))
            ->assertOk()
            ->assertViewIs('redirect.preview');

        $this->withHeader('User-Agent', 'Mozilla/5.0 Chrome/120.0')
            ->get(route('link.redirect', $redirectLink->alias))
            ->assertStatus(301)
            ->assertHeader('Location', 'https://example.com/target');

        $this->get(route('link.redirect', $passwordLink->alias))
            ->assertOk()
            ->assertViewIs('redirect.password');

        $this->post(route('link.password', $passwordLink->id), ['password' => 'secret'])
            ->assertStatus(301)
            ->assertHeader('Location', $passwordLink->url);

        $this->from(route('contact'))->post(route('locale'), ['locale' => 'en'])->assertRedirect(route('contact'));
    }

    public function test_authenticated_front_controller_methods(): void
    {
        $user = $this->user(['email' => 'front@example.test']);
        $workspace = $this->workspace($user, ['name' => 'Primary workspace']);
        $domain = $this->domain($user, ['name' => 'http://front-domain.test']);
        $link = $this->link($user, [
            'alias' => 'front-link',
            'workspace_id' => $workspace->id,
            'domain_id' => $domain->id,
            'clicks' => 5,
        ]);

        $this->actingAs($user);

        $this->get(route('dashboard'))->assertOk()->assertViewIs('dashboard.content');

        $this->get(route('links'))->assertOk()->assertViewIs('links.content');
        $this->get(route('links.edit', $link->id))->assertOk()->assertViewIs('links.content');
        $this->from(route('links'))->post(route('links.new'), [
            'url' => 'https://example.com/created',
            'alias' => 'created-link',
        ])->assertRedirect(route('links'));
        $this->assertDatabaseHas('links', ['alias' => 'created-link', 'user_id' => $user->id]);

        $this->from(route('links.edit', $link->id))->post(route('links.edit', $link->id), [
            'url' => 'https://example.com/updated',
            'alias' => 'front-link-updated',
        ])->assertRedirect(route('links.edit', $link->id));
        $this->assertDatabaseHas('links', ['id' => $link->id, 'alias' => 'front-link-updated']);

        $linkToDelete = $this->link($user, ['alias' => 'delete-me']);
        $this->post(route('links.delete', $linkToDelete->id))->assertRedirect(route('links'));
        $this->assertDatabaseMissing('links', ['id' => $linkToDelete->id]);

        $this->get(route('workspaces'))->assertOk()->assertViewIs('workspaces.content');
        $this->get(route('workspaces.new'))->assertOk()->assertViewIs('workspaces.content');
        $this->get(route('workspaces.edit', $workspace->id))->assertOk()->assertViewIs('workspaces.content');
        $this->post(route('workspaces.new'), ['name' => 'Created workspace', 'color' => 2])->assertRedirect(route('workspaces'));
        $this->assertDatabaseHas('workspaces', ['name' => 'Created workspace', 'user_id' => $user->id]);
        $this->from(route('workspaces.edit', $workspace->id))->post(route('workspaces.edit', $workspace->id), [
            'name' => 'Updated workspace',
            'color' => 3,
        ])->assertRedirect(route('workspaces.edit', $workspace->id));
        $this->assertDatabaseHas('workspaces', ['id' => $workspace->id, 'name' => 'Updated workspace']);
        $workspaceToDelete = $this->workspace($user, ['name' => 'Delete workspace']);
        $this->post(route('workspaces.delete', $workspaceToDelete->id))->assertRedirect(route('workspaces'));
        $this->assertDatabaseMissing('workspaces', ['id' => $workspaceToDelete->id]);

        $this->get(route('domains'))->assertOk()->assertViewIs('domains.content');
        $this->get(route('domains.new'))->assertOk()->assertViewIs('domains.content');
        $this->get(route('domains.edit', $domain->id))->assertOk()->assertViewIs('domains.content');
        $this->withServerVariables(['SERVER_ADDR' => '127.0.0.1'])
            ->post(route('domains.new'), ['name' => 'http://localhost'])
            ->assertRedirect(route('domains'));
        $this->assertDatabaseHas('domains', ['name' => 'localhost', 'user_id' => $user->id]);
        $this->from(route('domains.edit', $domain->id))->post(route('domains.edit', $domain->id), [
            'index_page' => 'https://example.com',
            'not_found_page' => 'https://example.com/404',
        ])->assertRedirect(route('domains.edit', $domain->id));
        $this->assertDatabaseHas('domains', ['id' => $domain->id, 'not_found_page' => 'https://example.com/404']);
        $domainToDelete = $this->domain($user);
        $this->post(route('domains.delete', $domainToDelete->id))->assertRedirect(route('domains'));
        $this->assertDatabaseMissing('domains', ['id' => $domainToDelete->id]);

        $this->get(route('settings'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.account'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.security'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.api'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.delete'))->assertOk()->assertViewIs('settings.content');

        $this->from(route('settings.account'))->post(route('settings.account.update'), [
            'name' => 'Updated User',
            'email' => 'front-updated@example.test',
            'timezone' => 'UTC',
        ])->assertRedirect(route('settings.account'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'front-updated@example.test']);

        $this->from(route('settings.security'))->post(route('settings.security.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('settings.security'));

        $this->from(route('settings.api'))->post(route('settings.api.update'))->assertRedirect(route('settings.api'));
        $this->assertNotNull($user->fresh()->api_token);
    }

    public function test_account_delete_controller_method(): void
    {
        $user = $this->user(['email' => 'delete-account@example.test']);

        $this->actingAs($user)
            ->from(route('settings.delete'))
            ->post(route('settings.account.delete'), ['current_password' => 'password'])
            ->assertRedirect(route('home'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_stats_controller_methods(): void
    {
        $user = $this->user();
        $link = $this->link($user, ['alias' => 'stats-link', 'clicks' => 5]);
        $this->stat($link);

        $this->actingAs($user);

        foreach ([
            route('stats', $link->id),
            route('stats.geographic', $link->id),
            route('stats.browsers', $link->id),
            route('stats.platforms', $link->id),
            route('stats.devices', $link->id),
            route('stats.languages', $link->id),
            route('stats.sources', $link->id),
            route('stats.social', $link->id),
        ] as $url) {
            $this->get($url)->assertOk()->assertViewIs('stats.content');
        }
    }

    public function test_payment_settings_controller_methods(): void
    {
        $plan = $this->paidPlan(['name' => 'Payment Pro']);
        $subscription = $this->subscription($this->user(), $plan);
        $paymentMethod = $this->paymentMethod();

        $user = Mockery::mock(User::class)->makePartial();
        $user->forceFill([
            'id' => $subscription->user_id,
            'name' => 'Payment User',
            'email' => 'payment@example.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'UTC',
            'role' => 0,
            'stripe_id' => 'cus_test_payment',
        ]);
        $user->exists = true;
        $user->setRelation('subscriptions', collect([$subscription]));
        $invoice = $this->fakeInvoice($user);
        $user->shouldReceive('invoices')->andReturn(collect([$invoice]));

        $this->be($user);

        $this->mock(PaymentSettingsService::class, function (MockInterface $mock) use ($subscription, $plan, $paymentMethod, $invoice): void {
            $mock->shouldReceive('subscriptionEditData')->andReturn([
                'subscription' => $subscription,
                'plan' => $plan,
            ])->byDefault();
            $mock->shouldReceive('paymentMethods')->andReturn([
                'defaultPaymentMethod' => $paymentMethod,
                'paymentMethods' => collect([$paymentMethod]),
            ])->byDefault();
            $mock->shouldReceive('newPaymentMethodData')->andReturn([
                'intent' => $this->setupIntent(),
                'hasDefaultPaymentMethod' => false,
            ])->byDefault();
            $mock->shouldReceive('editPaymentMethodData')->andReturn([
                'id' => $paymentMethod->id,
                'defaultPaymentMethod' => $paymentMethod,
                'paymentMethod' => $paymentMethod,
                'intent' => $this->setupIntent(),
            ])->byDefault();
            $mock->shouldReceive('billingCustomer')->andReturn($this->stripeCustomer())->byDefault();
            $mock->shouldReceive('invoice')->andReturn($invoice)->byDefault();
            $mock->shouldReceive('addPaymentMethod')->andReturn($paymentMethod)->byDefault();
            $mock->shouldReceive('updatePaymentMethod')->andReturnNull()->byDefault();
            $mock->shouldReceive('deletePaymentMethod')->andReturn($paymentMethod)->byDefault();
            $mock->shouldReceive('updateBilling')->andReturnNull()->byDefault();
            $mock->shouldReceive('cancelSubscription')->andReturnNull()->byDefault();
            $mock->shouldReceive('resumeSubscription')->andReturnNull()->byDefault();
        });

        $this->get(route('settings.payments.subscriptions'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.payments.subscriptions.edit', $subscription->id))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.payments.methods'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.payments.methods.new'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.payments.methods.edit', $paymentMethod->id))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.payments.billing'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.payments.invoices'))->assertOk()->assertViewIs('settings.content');
        $this->get(route('settings.payments.invoice', $invoice->id))->assertOk()->assertViewIs('settings.payments.invoice');

        $this->from(route('settings.payments.methods.new'))->post(route('settings.payments.methods.new'), [
            'payment_method' => $paymentMethod->id,
            'default' => 1,
        ])->assertRedirect(route('settings.payments.methods'));

        $this->from(route('settings.payments.methods.edit', $paymentMethod->id))
            ->post(route('settings.payments.methods.edit', $paymentMethod->id), ['default' => 1])
            ->assertRedirect(route('settings.payments.methods.edit', $paymentMethod->id));

        $this->post(route('settings.payments.methods.delete', $paymentMethod->id))
            ->assertRedirect(route('settings.payments.methods'));

        $this->from(route('settings.payments.billing'))->post(route('settings.payments.billing'), [
            'name' => 'Payment User',
            'address' => 'Main Street',
            'city' => 'New York',
            'postal_code' => '10001',
            'country' => 'US',
        ])->assertRedirect(route('settings.payments.billing'));

        $this->from(route('settings.payments.subscriptions.edit', $subscription->id))
            ->post(route('settings.payments.subscriptions.cancel', $subscription->name))
            ->assertRedirect(route('settings.payments.subscriptions.edit', $subscription->id));

        $this->from(route('settings.payments.subscriptions.edit', $subscription->id))
            ->post(route('settings.payments.subscriptions.resume', $subscription->name))
            ->assertRedirect(route('settings.payments.subscriptions.edit', $subscription->id));
    }

    public function test_pricing_and_checkout_controller_methods(): void
    {
        $user = $this->user(['email' => 'checkout@example.test']);
        $plan = $this->paidPlan(['name' => 'Checkout Pro']);
        $paymentMethod = $this->paymentMethod();

        $this->actingAs($user);

        $this->mock(CheckoutService::class, function (MockInterface $mock) use ($user, $plan, $paymentMethod): void {
            $mock->shouldReceive('prepareCheckout')->andReturn([
                'status' => CheckoutStatus::Ready,
                'data' => [
                    'plan' => $plan,
                    'user' => $user,
                    'paymentMethod' => $paymentMethod,
                    'customer' => $this->customer(),
                ],
            ])->byDefault();
            $mock->shouldReceive('prepareCollect')->andReturn([
                'status' => CheckoutStatus::Ready,
                'data' => [
                    'user' => $user,
                    'plan' => $plan,
                    'customer' => $this->customer(),
                    'intent' => $this->setupIntent(),
                ],
            ])->byDefault();
            $mock->shouldReceive('confirmationPayment')
                ->andThrow(new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException())
                ->byDefault();
            $mock->shouldReceive('subscribeForCheckout')->andReturn([
                'status' => CheckoutStatus::Complete,
            ])->byDefault();
            $mock->shouldReceive('updatePaymentDetails')->andReturnNull()->byDefault();
        });

        $this->get(route('pricing'))->assertOk()->assertViewIs('pricing.index');
        $this->get(route('checkout.index', ['id' => $plan->id, 'period' => 'monthly']))
            ->assertOk()
            ->assertViewIs('checkout.index');

        Session::put('redirect', ['id' => $plan->id]);
        $this->get(route('checkout.collect', ['period' => 'monthly']))
            ->assertOk()
            ->assertViewIs('checkout.collect');

        $this->get(route('checkout.confirm', 'pi_invalid'))->assertNotFound();
        $this->get(route('checkout.complete'))->assertOk()->assertViewIs('checkout.complete');
        $this->get(route('checkout.cancelled'))->assertOk()->assertViewIs('checkout.cancelled');

        $this->post(route('checkout.subscribe', ['id' => $plan->id, 'period' => 'monthly']))
            ->assertRedirect(route('checkout.complete'));

        Session::put('redirect', ['id' => $plan->id]);
        $this->from(route('checkout.collect', ['period' => 'monthly']))
            ->post(route('checkout.collect', ['period' => 'monthly']), [
                'payment_method' => $paymentMethod->id,
                'name' => 'Checkout User',
                'address' => 'Main Street',
                'city' => 'New York',
                'postal_code' => '10001',
                'country' => 'US',
            ])->assertRedirect(route('checkout.index', ['id' => $plan->id, 'period' => 'monthly']));
    }

    private function stripeCustomer(): StripeCustomer
    {
        return StripeCustomer::constructFrom([
            'id' => 'cus_test_payment',
            'name' => 'Payment User',
            'phone' => '+10000000000',
            'address' => [
                'line1' => 'Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
            ],
        ]);
    }

    private function fakeInvoice(User $user): CashierInvoice
    {
        $stripeInvoice = StripeInvoice::constructFrom([
            'id' => 'in_test',
            'number' => 'INV-001',
            'customer' => $user->stripe_id,
            'customer_name' => 'Payment User',
            'customer_phone' => '+10000000000',
            'customer_address' => [
                'line1' => 'Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
            ],
            'created' => Carbon::now()->timestamp,
            'currency' => 'usd',
            'subtotal' => 1000,
            'tax' => 0,
            'total' => 1000,
            'tax_percent' => null,
            'discounts' => [],
            'total_discount_amounts' => [],
            'starting_balance' => 0,
            'ending_balance' => null,
        ]);

        return new class($user, $stripeInvoice) extends CashierInvoice {
            public function invoiceItems(): array
            {
                return [];
            }

            public function subscriptions(): array
            {
                return [];
            }

            public function hasDiscount(): bool
            {
                return false;
            }

            public function hasStartingBalance(): bool
            {
                return false;
            }
        };
    }
}
