<?php

namespace Tests\Feature;

use App\Http\Controllers\InstallController;
use App\Http\Controllers\WebhookController;
use App\Http\Requests\Install\AdminRequest;
use App\Http\Requests\Install\DatabaseRequest;
use App\Models\Link;
use App\Models\Setting;
use App\Models\User;
use App\Services\UrlMetadataService;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ControllerMethodsCoverageTest extends TestCase
{
    use ControllerTestHelpers;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->withoutMiddleware(PreventRequestsDuringMaintenance::class);

        $this->mock(UrlMetadataService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('parse')->andReturn(['title' => 'Parsed title'])->byDefault();
        });
    }

    public function test_api_link_controller_methods_work_through_routes(): void
    {
        $user = $this->user([
            'email' => 'api-controller@example.test',
            'api_token' => Str::random(60),
        ]);
        $otherUser = $this->user(['email' => 'api-controller-other@example.test']);

        $ownedLink = $this->link($user, [
            'alias' => 'api-owned',
            'url' => 'https://owned.example.test',
        ]);
        $otherLink = $this->link($otherUser, [
            'alias' => 'api-hidden',
            'url' => 'https://hidden.example.test',
        ]);

        $this->api($user)
            ->getJson('/api/v1/links')
            ->assertOk()
            ->assertJsonFragment(['alias' => 'api-owned'])
            ->assertJsonMissing(['alias' => 'api-hidden']);

        $createResponse = $this->api($user)
            ->postJson('/api/v1/links', [
                'url' => 'https://created.example.test/path',
                'alias' => 'api-created',
            ])
            ->assertCreated()
            ->assertJsonPath('data.alias', 'api-created')
            ->assertJsonPath('data.url', 'https://created.example.test/path');

        $createdId = $createResponse->json('data.id');

        $this->assertDatabaseHas(Link::getTableName(), [
            'id' => $createdId,
            'user_id' => $user->id,
            'alias' => 'api-created',
            'title' => 'Parsed title',
        ]);

        $this->api($user)
            ->getJson('/api/v1/links/'.$createdId)
            ->assertOk()
            ->assertJsonPath('data.alias', 'api-created');

        $this->api($user)
            ->patchJson('/api/v1/links/'.$createdId, [
                'url' => 'https://updated.example.test',
                'alias' => 'api-updated',
                'public' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.alias', 'api-updated')
            ->assertJsonPath('data.url', 'https://updated.example.test')
            ->assertJsonPath('data.public', 1);

        $this->api($user)
            ->getJson('/api/v1/links/'.$otherLink->id)
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');

        $this->api($user)
            ->postJson('/api/v1/links', [
                'multi_link' => true,
                'urls' => "https://one.example.test\nhttps://two.example.test",
            ])
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');

        $this->api($user)
            ->deleteJson('/api/v1/links/'.$createdId)
            ->assertOk()
            ->assertJsonPath('id', $createdId)
            ->assertJsonPath('deleted', true);

        $this->assertDatabaseMissing(Link::getTableName(), ['id' => $createdId]);

        $this->api($user)
            ->deleteJson('/api/v1/links/'.$otherLink->id)
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');

        $this->assertDatabaseHas(Link::getTableName(), ['id' => $ownedLink->id]);
    }

    public function test_register_controller_registration_form_respects_setting(): void
    {
        Setting::where('name', 'registration_registration')->update(['value' => 1]);

        $this->get(route('register'))
            ->assertOk()
            ->assertViewIs('auth.register');

        Setting::where('name', 'registration_registration')->update(['value' => 0]);

        $this->get(route('register'))->assertNotFound();
    }

    public function test_webhook_controller_invoice_payment_succeeded_handler_returns_success(): void
    {
        $response = $this->app->make(WebhookController::class)->handleInvoicePaymentSucceeded();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_install_controller_redirects_to_database_without_saved_credentials(): void
    {
        $request = AdminRequest::create('/install/install-app', 'POST', [
            'name' => 'Installer Admin',
            'email' => 'installer-admin@example.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response = $this->app->make(InstallController::class)->install($request);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('install.database'), $response->getTargetUrl());
    }

    public function test_installer_database_request_returns_validation_errors(): void
    {
        $validator = Validator::make([], (new DatabaseRequest())->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('db_host', $validator->errors()->messages());
        $this->assertArrayHasKey('db_port', $validator->errors()->messages());
        $this->assertArrayHasKey('db_database', $validator->errors()->messages());
        $this->assertArrayHasKey('db_username', $validator->errors()->messages());
    }

    public function test_install_controller_installation_returns_database_connection_errors(): void
    {
        $request = DatabaseRequest::create('/install/installation', 'POST', [
            'db_host' => '127.0.0.1',
            'db_port' => 1,
            'db_database' => 'missing_database',
            'db_username' => 'missing_user',
            'db_password' => 'missing_password',
        ]);
        $request->setLaravelSession($this->app['session.store']);

        $response = $this->app->make(InstallController::class)->installation($request);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('install.database'), $response->getTargetUrl());
        $this->assertTrue(session()->has('errors'));
    }

    private function api(User $user): self
    {
        return $this->withHeader('Authorization', 'Bearer '.$user->api_token);
    }
}
