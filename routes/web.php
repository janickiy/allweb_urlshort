<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevelopersController;
use App\Http\Controllers\DomainsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinksController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SpacesController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/install', fn () => response('Run composer install, configure .env, and execute php artisan migrate --seed.', 200))
    ->name('install');

Route::post('/lang', [LocaleController::class, 'index'])->name('locale');

Route::middleware('installed')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::post('/shorten', [HomeController::class, 'createLink'])
    ->middleware('throttle:10,1')
    ->name('guest');

Route::controller(ContactController::class)->group(function () {
    Route::get('/contact', 'index')->name('contact');
    Route::post('/contact', 'sendMail')->middleware('throttle:5,10');
});

Route::get('/page/{url}', [PageController::class, 'index'])->name('page');
Route::get('/developers', [DevelopersController::class, 'index'])->name('developers');
Route::get('/qr/{id}', [QRController::class, 'index'])->name('qr');

Route::prefix('stats')
    ->controller(StatsController::class)
    ->group(function () {
        Route::get('{id}', 'index')->name('stats');
        Route::get('{id}/geographic', 'geographic')->name('stats.geographic');
        Route::get('{id}/browsers', 'browsers')->name('stats.browsers');
        Route::get('{id}/platforms', 'platforms')->name('stats.platforms');
        Route::get('{id}/devices', 'devices')->name('stats.devices');
        Route::get('{id}/languages', 'languages')->name('stats.languages');
        Route::get('{id}/sources', 'sources')->name('stats.sources');
        Route::get('{id}/social', 'social')->name('stats.social');
    });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('links')
        ->controller(LinksController::class)
        ->group(function () {
            Route::get('/', 'index')->name('links');
            Route::get('edit/{id}', 'linksEdit')->name('links.edit');
            Route::post('edit/{id}', 'updateLink');
            Route::post('new', 'createLink')->name('links.new');
            Route::post('delete/{id}', 'deleteLink')->name('links.delete');
        });

    Route::prefix('spaces')
        ->controller(SpacesController::class)
        ->group(function () {
            Route::get('/', 'index')->name('spaces');
            Route::get('new', 'spacesNew')->name('spaces.new');
            Route::post('new', 'createSpace');
            Route::get('edit/{id}', 'spacesEdit')->name('spaces.edit');
            Route::post('edit/{id}', 'updateSpace');
            Route::post('delete/{id}', 'deleteSpace')->name('spaces.delete');
        });

    Route::prefix('domains')
        ->controller(DomainsController::class)
        ->group(function () {
            Route::get('/', 'index')->name('domains');
            Route::get('new', 'domainsNew')->name('domains.new');
            Route::post('new', 'createDomain');
            Route::get('edit/{id}', 'domainsEdit')->name('domains.edit');
            Route::post('edit/{id}', 'updateDomain');
            Route::post('delete/{id}', 'deleteDomain')->name('domains.delete');
        });

    Route::prefix('settings')
        ->controller(SettingsController::class)
        ->group(function () {
            Route::get('/', 'index')->name('settings');

            Route::name('settings.')->group(function () {
                Route::get('account', 'account')->name('account');
                Route::post('account', 'updateAccount')->name('account.update');
                Route::get('security', 'security')->name('security');
                Route::post('security', 'updateSecurity')->name('security.update');
                Route::get('api', 'api')->name('api');
                Route::post('api', 'updateApi')->name('api.update');
                Route::get('delete', 'delete')->name('delete');
                Route::post('delete', 'deleteAccount')->name('account.delete');
            });

            Route::prefix('payments')
                ->middleware('payment')
                ->group(function () {
                    Route::get('methods', 'paymentMethods')->name('settings.payments.methods');
                    Route::get('methods/new', 'paymentMethodsNew')->name('settings.payments.methods.new');
                    Route::post('methods/new', 'createPaymentMethod');
                    Route::get('methods/edit/{id}', 'paymentMethodsEdit')->name('settings.payments.methods.edit');
                    Route::post('methods/edit/{id}', 'updatePaymentMethod');
                    Route::post('methods/delete/{id}', 'deletePaymentMethod')->name('settings.payments.methods.delete');

                    Route::get('subscriptions', 'subscriptions')->name('settings.payments.subscriptions');
                    Route::get('subscriptions/edit/{id}', 'subscriptionsEdit')->name('settings.payments.subscriptions.edit');
                    Route::post('subscriptions/cancel/{subscription}', 'cancelSubscription')->name('settings.payments.subscriptions.cancel');
                    Route::post('subscriptions/resume/{subscription}', 'resumeSubscription')->name('settings.payments.subscriptions.resume');

                    Route::get('invoices', 'invoices')->name('settings.payments.invoices');
                    Route::get('invoice/{invoice}', 'invoice')->name('settings.payments.invoice');
                    Route::get('billing', 'billing')->name('settings.payments.billing');
                    Route::post('billing', 'updateBilling');
                });
        });

    Route::prefix('checkout')
        ->middleware('payment')
        ->controller(CheckoutController::class)
        ->group(function () {
            Route::get('collect/{period}', 'collect')->name('checkout.collect');
            Route::post('collect/{period}', 'updatePaymentDetails');
            Route::get('confirm/{id}', 'show')->name('checkout.confirm');
            Route::get('cancelled', 'cancelled')->name('checkout.cancelled');
            Route::get('complete', 'complete')->name('checkout.complete');
            Route::get('{id}/{period}', 'index')->name('checkout.index');
            Route::post('subscribe/{id}/{period}', 'subscribe')->name('checkout.subscribe');
        });
});

Route::prefix('admin')
    ->middleware(['auth', 'verified', 'admin'])
    ->controller(AdminController::class)
    ->group(function () {
        Route::redirect('/', 'admin/dashboard');

        Route::name('admin.')->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');

            Route::prefix('settings')
                ->name('settings.')
                ->group(function () {
                    $settings = [
                        'general' => ['settingsGeneral', 'updateSettingsGeneral'],
                        'appearance' => ['settingsAppearance', 'updateSettingsAppearance'],
                        'email' => ['settingsEmail', 'updateSettingsEmail'],
                        'social' => ['settingsSocial', 'updateSettingsSocial'],
                        'payment' => ['settingsPayment', 'updateSettingsPayment'],
                        'invoice' => ['settingsInvoice', 'updateSettingsInvoice'],
                        'registration' => ['settingsRegistration', 'updateSettingsRegistration'],
                        'contact' => ['settingsContact', 'updateSettingsContact'],
                        'legal' => ['settingsLegal', 'updateSettingsLegal'],
                        'captcha' => ['settingsCaptcha', 'updateSettingsCaptcha'],
                        'shortener' => ['settingsShortener', 'updateSettingsShortener'],
                    ];

                    foreach ($settings as $section => [$show, $update]) {
                        Route::get($section, $show)->name($section);
                        Route::post($section.'/update', $update)->name($section.'.update');
                    }
                });

            Route::prefix('languages')
                ->group(function () {
                    Route::get('/', 'languages')->name('languages');

                    Route::name('languages.')->group(function () {
                        Route::get('new', 'languagesNew')->name('new');
                        Route::post('new', 'createLanguage')->name('create');
                        Route::get('edit/{id}', 'languagesEdit')->name('edit');
                        Route::post('edit/{id}', 'updateLanguage')->name('update');
                        Route::post('delete/{id}', 'deleteLanguage')->name('delete');
                    });
                });

            Route::prefix('users')
                ->group(function () {
                    Route::get('/', 'users')->name('users');

                    Route::name('users.')->group(function () {
                        Route::get('edit/{id}', 'usersEdit')->name('edit');
                        Route::post('delete/{id}', 'deleteUser')->name('delete');
                        Route::post('disable/{id}', 'disableUser')->name('disable');
                        Route::post('restore/{id}', 'restoreUser')->name('restore');
                    });

                    Route::post('edit/{id}', 'updateUser')->name('subscriptions.update');
                });

            Route::prefix('links')
                ->group(function () {
                    Route::get('/', 'links')->name('links');

                    Route::name('links.')->group(function () {
                        Route::get('edit/{id}', 'linksEdit')->name('edit');
                        Route::post('edit/{id}', 'updateLink')->name('update');
                        Route::post('delete/{id}', 'deleteLink')->name('delete');
                    });
                });

            Route::prefix('spaces')
                ->group(function () {
                    Route::get('/', 'spaces')->name('spaces');

                    Route::name('spaces.')->group(function () {
                        Route::get('edit/{id}', 'spacesEdit')->name('edit');
                        Route::post('edit/{id}', 'updateSpace')->name('update');
                        Route::post('delete/{id}', 'deleteSpace')->name('delete');
                    });
                });

            Route::prefix('domains')
                ->group(function () {
                    Route::get('/', 'domains')->name('domains');

                    Route::name('domains.')->group(function () {
                        Route::get('edit/{id}', 'domainsEdit')->name('edit');
                        Route::post('edit/{id}', 'updateDomain')->name('update');
                        Route::post('delete/{id}', 'deleteDomain')->name('delete');
                    });
                });

            Route::prefix('pages')
                ->group(function () {
                    Route::get('/', 'pages')->name('pages');

                    Route::name('pages.')->group(function () {
                        Route::get('new', 'pagesNew')->name('new');
                        Route::post('new', 'createPage')->name('create');
                        Route::get('edit/{id}', 'pagesEdit')->name('edit');
                        Route::post('edit/{id}', 'updatePage')->name('update');
                        Route::post('delete/{id}', 'deletePage')->name('delete');
                    });
                });

            Route::prefix('plans')
                ->group(function () {
                    Route::get('/', 'plans')->name('plans');

                    Route::name('plans.')->group(function () {
                        Route::get('new', 'plansNew')->middleware('payment')->name('new');
                        Route::post('new', 'createPlan')->middleware('payment')->name('create');
                        Route::get('edit/{id}', 'plansEdit')->name('edit');
                        Route::post('edit/{id}', 'updatePlan')->name('update');
                        Route::post('disable/{id}', 'disablePlan')->middleware('payment')->name('disable');
                        Route::post('restore/{id}', 'restorePlan')->middleware('payment')->name('restore');
                    });
                });

            Route::prefix('subscriptions')
                ->group(function () {
                    Route::get('/', 'subscriptions')->name('subscriptions');

                    Route::name('subscriptions.')->group(function () {
                        Route::get('new', 'subscriptionsNew')->middleware('payment')->name('new');
                        Route::post('new', 'createSubscription')->middleware('payment')->name('create');
                        Route::get('edit/{id}', 'subscriptionsEdit')->name('edit');
                        Route::post('delete/{id}', 'deleteSubscription')->name('delete');
                    });
                });
        });
    });

Route::prefix('pricing')
    ->middleware('payment')
    ->controller(PricingController::class)
    ->group(function () {
        Route::get('/', 'index')->name('pricing');
    });

Route::post('stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('stripe.webhook');

Route::controller(RedirectController::class)->group(function () {
    Route::get('/{id}/+', 'index')->name('link.preview');
    Route::get('/{id}', 'index')->name('link.redirect');
    Route::post('/{id}', 'validatePassword')->name('link.password');
});
