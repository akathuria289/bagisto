<?php

use Webkul\SocialLogin\Http\Controllers\LoginController;

Route::group(['middleware' => ['web', 'locale', 'theme', 'currency']], function () {
    Route::controller(LoginController::class)->prefix('customer/social-login/{provider}')->group(function () {
        Route::get('',  'redirectToProvider')->defaults('_config', [
            'redirect' => 'shop.customer.profile.index'
        ])->name('customer.social-login.index');

        Route::get('callback', 'handleProviderCallback')->defaults('_config', [
            'redirect' => 'shop.customer.profile.index'
        ])->name('customer.social-login.callback');
    });
});