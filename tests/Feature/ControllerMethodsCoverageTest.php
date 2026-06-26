<?php

namespace Tests\Feature;

use App\Http\Controllers\Installer\DatabaseController;
use App\Http\Controllers\Installer\EnvironmentController;
use App\Http\Controllers\WebhookController;
use App\Models\Link;
use App\Models\Setting;
use App\Models\User;
use App\Services\UserRegistrationService;
use App\Services\UrlMetadataService;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
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

    public function test_installer_database_controller_runs_migrations_and_creates_admin(): void
    {
        $this->registerInstallerRoute('LaravelInstaller::final', '/installer/final-test');

        request()->replace([
            'name' => 'Installer Admin',
            'email' => 'installer-admin@example.test',
            'password' => 'secret123',
        ]);

        $console = Mockery::mock(ConsoleKernel::class);
        $console->shouldReceive('call')
            ->once()
            ->with('migrate', ['--seed' => true, '--force' => true])
            ->andReturn(0);
        $console->shouldReceive('output')
            ->once()
            ->andReturn("Migrated\nSeeded\n");

        $registrations = Mockery::mock(UserRegistrationService::class);
        $registrations->shouldReceive('createInstallerAdmin')
            ->once()
            ->with([
                'name' => 'Installer Admin',
                'email' => 'installer-admin@example.test',
                'password' => 'secret123',
            ])
            ->andReturn(new User());

        $response = (new DatabaseController($console, $registrations))->database();

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('LaravelInstaller::final'), $response->getTargetUrl());
        $this->assertSame("Migrated\nSeeded", session('message'));
    }

    public function test_installer_environment_controller_returns_validation_errors(): void
    {
        $this->registerInstallerRoute('LaravelInstaller::environmentWizard', '/installer/environment-test');

        $response = $this->app->make(EnvironmentController::class)->saveWizard(
            Request::create('/install/environment/wizard', 'POST', []),
            $this->app->make(Redirector::class),
        );

        TestResponse::fromBaseResponse($response)
            ->assertRedirect(route('LaravelInstaller::environmentWizard'))
            ->assertSessionHasErrors(['app_name']);
    }

    public function test_installer_environment_controller_returns_database_connection_errors(): void
    {
        $this->registerInstallerRoute('LaravelInstaller::environmentWizard', '/installer/environment-test');

        $response = $this->app->make(EnvironmentController::class)->saveWizard(
            Request::create('/install/environment/wizard', 'POST', $this->environmentPayload([
                'database_connection' => 'sqlite',
                'database_name' => '/missing-dir/test.sqlite',
            ])),
            $this->app->make(Redirector::class),
        );

        TestResponse::fromBaseResponse($response)
            ->assertRedirect(route('LaravelInstaller::environmentWizard'))
            ->assertSessionHasErrors(['database_connection']);
    }

    private function api(User $user): self
    {
        return $this->withHeader('Authorization', 'Bearer '.$user->api_token);
    }

    private function registerInstallerRoute(string $name, string $uri): void
    {
        Route::get($uri, fn () => response('installer'))->name($name);
        Route::getRoutes()->refreshNameLookups();
    }

    /**
     * Return a valid installer environment payload that can be adjusted per test.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function environmentPayload(array $overrides = []): array
    {
        return array_merge([
            'app_name' => 'ShortLink Pro',
            'environment' => 'local',
            'environment_custom' => '',
            'app_debug' => 'true',
            'app_log_level' => 'debug',
            'app_url' => 'https://example.test',
            'database_connection' => 'sqlite',
            'database_hostname' => 'localhost',
            'database_port' => 3306,
            'database_name' => ':memory:',
            'database_username' => 'root',
            'database_password' => '',
            'broadcast_driver' => 'log',
            'cache_driver' => 'array',
            'session_driver' => 'array',
            'queue_driver' => 'sync',
            'redis_hostname' => '127.0.0.1',
            'redis_password' => 'null',
            'redis_port' => 6379,
            'mail_driver' => 'array',
            'mail_host' => 'localhost',
            'mail_port' => '1025',
            'mail_username' => 'null',
            'mail_password' => 'null',
            'mail_encryption' => 'null',
            'mail_from_address' => 'mail@example.test',
            'mail_from_name' => 'ShortLink Pro',
            'pusher_app_id' => '',
            'pusher_app_key' => '',
            'pusher_app_secret' => '',
            'name' => 'Installer Admin',
            'email' => 'installer-admin@example.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ], $overrides);
    }
}
