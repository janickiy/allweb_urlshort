<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'middleware' => ['web', 'install']], function() {
    Route::post('environment/saveWizard', [
        'as' => 'environmentSaveWizard',
        'uses' => 'Installer\EnvironmentController@saveWizard'
    ]);

    Route::get('database', [
        'as' => 'database',
        'uses' => 'Installer\DatabaseController@database'
    ]);
});

Route::post('/lang', 'LocaleController@index')->name('locale');

// Home Routes
Route::get('/', 'HomeController@index')->middleware('installed')->name('home');
Route::post('/shorten', 'HomeController@createLink')->middleware('throttle:10,1')->name('guest');

// Contact Routes
Route::get('/contact', 'ContactController@index')->name('contact');
Route::post('/contact', 'ContactController@sendMail')->middleware('throttle:5,10');

// Pages Routes
Route::get('/page/{url}', 'PageController@index')->name('page');

// Developers Routes
Route::get('/developers', 'DevelopersController@index')->name('developers');

// User Routes
Route::get('/dashboard', 'DashboardController@index')->middleware('verified')->name('dashboard');

// links
Route::prefix('links')->group(function () {
    Route::get('', 'LinksController@index')->middleware('verified')->name('links');
    Route::get('edit/{id}', 'LinksController@linksEdit')->middleware('verified')->name('links.edit');
    Route::post('edit/{id}', 'LinksController@updateLink');
    Route::post('new', 'LinksController@createLink')->name('links.new');
    Route::post('delete/{id}', 'LinksController@deleteLink')->name('links.delete');
});

// spaces
Route::prefix('spaces')->group(function () {
    Route::get('', 'SpacesController@index')->middleware('verified')->name('spaces');
    Route::get('new', 'SpacesController@spacesNew')->middleware('verified')->name('spaces.new');
    Route::post('new', 'SpacesController@createSpace');
    Route::get('edit/{id}', 'SpacesController@spacesEdit')->middleware('verified')->name('spaces.edit');
    Route::post('edit/{id}', 'SpacesController@updateSpace');
    Route::post('delete/{id}', 'SpacesController@deleteSpace')->name('spaces.delete');
});

// domains
Route::prefix('domains')->group(function () {
    Route::get('', 'DomainsController@index')->middleware('verified')->name('domains');
    Route::get('new', 'DomainsController@domainsNew')->middleware('verified')->name('domains.new');
    Route::post('new', 'DomainsController@createDomain');
    Route::get('edit/{id}', 'DomainsController@domainsEdit')->middleware('verified')->name('domains.edit');
    Route::post('edit/{id}', 'DomainsController@updateDomain');
    Route::post('delete/{id}', 'DomainsController@deleteDomain')->name('domains.delete');
});

// stats
Route::prefix('stats')->group(function () {
    Route::get('{id}', 'StatsController@index')->name('stats');
    Route::get('{id}/geographic', 'StatsController@geographic')->name('stats.geographic');
    Route::get('{id}/browsers', 'StatsController@browsers')->name('stats.browsers');
    Route::get('{id}/platforms', 'StatsController@platforms')->name('stats.platforms');
    Route::get('{id}/devices', 'StatsController@devices')->name('stats.devices');
    Route::get('{id}/languages', 'StatsController@languages')->name('stats.languages');
    Route::get('{id}/sources', 'StatsController@sources')->name('stats.sources');
    Route::get('{id}/social', 'StatsController@social')->name('stats.social');
});


Route::get('/qr/{id}', 'QRController@index')->name('qr');


// settings
Route::prefix('settings')->middleware('verified')->group(function () {
    Route::get('/', 'SettingsController@index')->name('settings');

    Route::get('account', 'SettingsController@account')->name('settings.account');
    Route::get('security', 'SettingsController@security')->name('settings.security');
    Route::get('api', 'SettingsController@api')->name('settings.api');
    Route::get('delete', 'SettingsController@delete')->name('settings.delete');

    // payments
    Route::prefix('payments')->group(function () {
        Route::get('methods', 'SettingsController@paymentMethods')->middleware('payment')->name('settings.payments.methods');
        Route::get('methods/new', 'SettingsController@paymentMethodsNew')->middleware('payment')->name('settings.payments.methods.new');
        Route::get('methods/edit/{id}', 'SettingsController@paymentMethodsEdit')->middleware('payment')->name('settings.payments.methods.edit');

        Route::get('subscriptions', 'SettingsController@subscriptions')->middleware('payment')->name('settings.payments.subscriptions');
        Route::get('subscriptions/edit/{id}', 'SettingsController@subscriptionsEdit')->middleware('payment')->name('settings.payments.subscriptions.edit');

        Route::get('invoices', 'SettingsController@invoices')->middleware('payment')->name('settings.payments.invoices');
        Route::get('invoice/{invoice}', 'SettingsController@invoice')->middleware('payment')->name('settings.payments.invoice');

        Route::get('billing', 'SettingsController@billing')->middleware('payment')->name('settings.payments.billing');

        Route::post('methods/new', 'SettingsController@createPaymentMethod')->middleware('payment');
        Route::post('methods/edit/{id}', 'SettingsController@updatePaymentMethod')->middleware('payment');
        Route::post('methods/delete/{id}', 'SettingsController@deletePaymentMethod')->middleware('payment')->name('settings.payments.methods.delete');

        Route::post('subscriptions/cancel/{subscription}', 'SettingsController@cancelSubscription')->middleware('payment')->name('settings.payments.subscriptions.cancel');
        Route::post('subscriptions/resume/{subscription}', 'SettingsController@resumeSubscription')->middleware('payment')->name('settings.payments.subscriptions.resume');

        Route::post('billing', 'SettingsController@updateBilling')->middleware('payment');

    });


    Route::post('account', 'SettingsController@updateAccount')->name('settings.account.update');
    Route::post('security', 'SettingsController@updateSecurity')->name('settings.security.update');
    Route::post('delete', 'SettingsController@deleteAccount')->name('settings.account.delete');



    Route::post('api', 'SettingsController@updateApi')->name('settings.api.update');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::redirect('/', 'admin/dashboard');

    Route::get('/dashboard', 'AdminController@dashboard')->name('admin.dashboard');

    // settings
    Route::prefix('settings')->group(function () {

        // general
        Route::prefix('general')->group(function () {
            Route::get('', 'AdminController@settingsGeneral')->name('admin.settings.general');
            Route::post('update', 'AdminController@updateSettingsGeneral')->name('admin.settings.general.update');
        });

        // appearance
        Route::prefix('appearance')->group(function () {
            Route::get('', 'AdminController@settingsAppearance')->name('admin.settings.appearance');
            Route::post('update', 'AdminController@updateSettingsAppearance')->name('admin.settings.appearance.update');
        });

        // email
        Route::prefix('email')->group(function () {
            Route::get('', 'AdminController@settingsEmail')->name('admin.settings.email');
            Route::post('update', 'AdminController@updateSettingsEmail')->name('admin.settings.email.update');
        });

        // social
        Route::prefix('social')->group(function () {
            Route::get('', 'AdminController@settingsSocial')->name('admin.settings.social');
            Route::post('update', 'AdminController@updateSettingsSocial')->name('admin.settings.social.update');
        });

        // payment
        Route::prefix('payment')->group(function () {
            Route::get('', 'AdminController@settingsPayment')->name('admin.settings.payment');
            Route::post('update', 'AdminController@updateSettingsPayment')->name('admin.settings.payment.update');
        });

        // invoice
        Route::prefix('invoice')->group(function () {
            Route::get('', 'AdminController@settingsInvoice')->name('admin.settings.invoice');
            Route::post('update', 'AdminController@updateSettingsInvoice')->name('admin.settings.invoice.update');
        });

        // registration
        Route::prefix('registration')->group(function () {
            Route::get('', 'AdminController@settingsRegistration')->name('admin.settings.registration');
            Route::post('update', 'AdminController@updateSettingsRegistration')->name('admin.settings.registration.update');
        });

        // contact
        Route::prefix('contact')->group(function () {
            Route::get('', 'AdminController@settingsContact')->name('admin.settings.contact');
            Route::post('update', 'AdminController@updateSettingsContact')->name('admin.settings.contact.update');
        });

        Route::prefix('legal')->group(function () {
            Route::get('', 'AdminController@settingsLegal')->name('admin.settings.legal');
            Route::post('update', 'AdminController@updateSettingsLegal')->name('admin.settings.legal.update');
        });

        Route::prefix('captcha')->group(function () {
            Route::get('', 'AdminController@settingsCaptcha')->name('admin.settings.captcha');
            Route::post('update', 'AdminController@updateSettingsCaptcha')->name('admin.settings.captcha.update');
        });

        Route::prefix('shortener')->group(function () {
            Route::get('', 'AdminController@settingsShortener')->name('admin.settings.shortener');
            Route::post('update', 'AdminController@updateSettingsShortener')->name('admin.settings.shortener.update');
        });

        Route::group(['prefix' => 'datatable'], function () {
            Route::any('templates', 'DataTableController@getTemplates')->name('admin.datatable.templates');
        });

    });

    // languages
    Route::prefix('languages')->group(function () {
        Route::get('', 'AdminController@languages')->name('admin.languages');
        Route::get('new', 'AdminController@languagesNew')->name('admin.languages.new');
        Route::post('new', 'AdminController@createLanguage')->name('admin.languages.create');
        Route::get('edit/{id}', 'AdminController@languagesEdit')->name('admin.languages.edit');
        Route::post('edit/{id}', 'AdminController@updateLanguage')->name('admin.languages.update');
        Route::post('delete/{id}', 'AdminController@deleteLanguage')->name('admin.languages.delete');
    });

    // users
    Route::prefix('users')->group(function () {
        Route::get('', 'AdminController@users')->name('admin.users');
        Route::get('edit/{id}', 'AdminController@usersEdit')->name('admin.users.edit');
        Route::post('edit/{id}', 'AdminController@updateUser')->name('admin.subscriptions.update');
        Route::post('delete/{id}', 'AdminController@deleteUser')->name('admin.users.delete');
        Route::post('disable/{id}', 'AdminController@disableUser')->name('admin.users.disable');
        Route::post('restore/{id}', 'AdminController@restoreUser')->name('admin.users.restore');
    });

    // links
    Route::prefix('links')->group(function () {
        Route::get('', 'AdminController@links')->name('admin.links');
        Route::get('edit/{id}', 'AdminController@linksEdit')->name('admin.links.edit');
        Route::post('edit/{id}', 'AdminController@updateLink')->name('admin.links.update');
        Route::post('delete/{id}', 'AdminController@deleteLink')->name('admin.links.delete');
    });

    // links
    Route::prefix('spaces')->group(function () {
        Route::get('', 'AdminController@spaces')->name('admin.spaces');
        Route::get('edit/{id}', 'AdminController@spacesEdit')->name('admin.spaces.edit');
        Route::post('edit/{id}', 'AdminController@updateSpace')->name('admin.spaces.update');
        Route::post('delete/{id}', 'AdminController@deleteSpace')->name('admin.spaces.delete');
    });

    // domains
    Route::prefix('domains')->group(function () {
        Route::get('', 'AdminController@domains')->name('admin.domains');
        Route::get('edit/{id}', 'AdminController@domainsEdit')->name('admin.domains.edit');
        Route::post('edit/{id}', 'AdminController@updateDomain')->name('admin.domains.update');
        Route::post('delete/{id}', 'AdminController@deleteDomain')->name('admin.domains.delete');
    });

    // pages
    Route::prefix('pages')->group(function () {
        Route::get('', 'AdminController@pages')->name('admin.pages');
        Route::get('new', 'AdminController@pagesNew')->name('admin.pages.new');
        Route::post('new', 'AdminController@createPage')->name('admin.pages.create');
        Route::get('edit/{id}', 'AdminController@pagesEdit')->name('admin.pages.edit');
        Route::post('edit/{id}', 'AdminController@updatePage')->name('admin.pages.update');
        Route::post('delete/{id}', 'AdminController@deletePage')->name('admin.pages.delete');
    });

    Route::prefix('plans')->group(function () {
        Route::get('/', 'AdminController@plans')->name('admin.plans');
        Route::get('new', 'AdminController@plansNew')->middleware('payment')->name('admin.plans.new');
        Route::get('edit/{id}', 'AdminController@plansEdit')->name('admin.plans.edit');
        Route::post('new', 'AdminController@createPlan')->middleware('payment')->name('admin.plans.create');
        Route::post('edit/{id}', 'AdminController@updatePlan')->name('admin.plans.update');
        Route::post('disable/{id}', 'AdminController@disablePlan')->middleware('payment')->name('admin.plans.disable');
        Route::post('restore/{id}', 'AdminController@restorePlan')->middleware('payment')->name('admin.plans.restore');
    });


    Route::prefix('subscriptions')->group(function () {
        Route::get('', 'AdminController@subscriptions')->name('admin.subscriptions');
        Route::get('new', 'AdminController@subscriptionsNew')->middleware('payment')->name('admin.subscriptions.new');
        Route::post('new', 'AdminController@createSubscription')->middleware('payment')->name('admin.subscriptions.create');
        Route::get('edit/{id}', 'AdminController@subscriptionsEdit')->name('admin.subscriptions.edit');
        Route::post('delete/{id}', 'AdminController@deleteSubscription')->name('admin.subscriptions.delete');
    });

});


// Pricing Routes
Route::prefix('pricing')->middleware('payment')->group(function () {
    Route::get('/', 'PricingController@index')->name('pricing');
});

// Checkout Routes
Route::prefix('checkout')->middleware('verified', 'payment')->group(function () {
    Route::get('/collect/{period}', 'CheckoutController@collect')->name('checkout.collect');
    Route::post('/collect/{period}', 'CheckoutController@updatePaymentDetails');
    Route::get('/confirm/{id}', 'CheckoutController@show')->name('checkout.confirm');
    Route::get('/cancelled', 'CheckoutController@cancelled')->name('checkout.cancelled');
    Route::get('/complete', 'CheckoutController@complete')->name('checkout.complete');
    Route::get('/{id}/{period}', 'CheckoutController@index')->name('checkout.index');
    Route::post('/subscribe/{id}/{period}', 'CheckoutController@subscribe')->name('checkout.subscribe');
});

// Stripe Webhook Routes
Route::post('stripe/webhook', 'WebhookController@handleWebhook')->name('stripe.webhook');

// Redirect Routes
Route::get('/{id}/+', 'RedirectController@index')->name('link.preview');
Route::get('/{id}', 'RedirectController@index')->name('link.redirect');
Route::post('/{id}', 'RedirectController@validatePassword')->name('link.password');
