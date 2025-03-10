<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Core\ChannelController;
use Webkul\Admin\Http\Controllers\Core\CurrencyController;
use Webkul\Admin\Http\Controllers\Core\ExchangeRateController;
use Webkul\Admin\Http\Controllers\Core\LocaleController;
use Webkul\Admin\Http\Controllers\Core\SliderController;
use Webkul\Admin\Http\Controllers\Inventory\InventorySourceController;
use Webkul\Admin\Http\Controllers\Tax\TaxCategoryController;
use Webkul\Admin\Http\Controllers\Tax\TaxRateController;
use Webkul\Admin\Http\Controllers\User\RoleController;
use Webkul\Admin\Http\Controllers\User\UserController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['admin'], 'prefix' => config('app.admin_url')], function () {
    /**
     * Roles routes.
     */
    Route::get('roles', [RoleController::class, 'index'])->defaults('_config', [
        'view' => 'admin::users.roles.index',
    ])->name('admin.roles.index');

    Route::get('roles/create', [RoleController::class, 'create'])->defaults('_config', [
        'view' => 'admin::users.roles.create',
    ])->name('admin.roles.create');

    Route::post('roles/create', [RoleController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.roles.index',
    ])->name('admin.roles.store');

    Route::get('roles/edit/{id}', [RoleController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::users.roles.edit',
    ])->name('admin.roles.edit');

    Route::put('roles/edit/{id}', [RoleController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.roles.index',
    ])->name('admin.roles.update');

    Route::post('roles/delete/{id}', [RoleController::class, 'destroy'])->name('admin.roles.delete');

    /**
     * Locales routes.
     */
    Route::get('locales', [LocaleController::class, 'index'])->defaults('_config', [
        'view' => 'admin::settings.locales.index',
    ])->name('admin.locales.index');

    Route::get('locales/create', [LocaleController::class, 'create'])->defaults('_config', [
        'view' => 'admin::settings.locales.create',
    ])->name('admin.locales.create');

    Route::post('locales/create', [LocaleController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.locales.index',
    ])->name('admin.locales.store');

    Route::get('locales/edit/{id}', [LocaleController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::settings.locales.edit',
    ])->name('admin.locales.edit');

    Route::put('locales/edit/{id}', [LocaleController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.locales.index',
    ])->name('admin.locales.update');

    Route::post('locales/delete/{id}', [LocaleController::class, 'destroy'])->name('admin.locales.delete');

    /**
     * Currencies routes.
     */
    Route::get('currencies', [CurrencyController::class, 'index'])->defaults('_config', [
        'view' => 'admin::settings.currencies.index',
    ])->name('admin.currencies.index');

    Route::get('currencies/create', [CurrencyController::class, 'create'])->defaults('_config', [
        'view' => 'admin::settings.currencies.create',
    ])->name('admin.currencies.create');

    Route::post('currencies/create', [CurrencyController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.currencies.index',
    ])->name('admin.currencies.store');

    Route::get('currencies/edit/{id}', [CurrencyController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::settings.currencies.edit',
    ])->name('admin.currencies.edit');

    Route::put('currencies/edit/{id}', [CurrencyController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.currencies.index',
    ])->name('admin.currencies.update');

    Route::post('currencies/delete/{id}', [CurrencyController::class, 'destroy'])->name('admin.currencies.delete');

    Route::post('currencies/mass-delete', [CurrencyController::class, 'massDestroy'])->name('admin.currencies.mass_delete');

    /**
     * Exchange rates routes.
     */
    Route::get('exchange-rates', [ExchangeRateController::class, 'index'])->defaults('_config', [
        'view' => 'admin::settings.exchange_rates.index',
    ])->name('admin.exchange_rates.index');

    Route::get('exchange-rates/create', [ExchangeRateController::class, 'create'])->defaults('_config', [
        'view' => 'admin::settings.exchange_rates.create',
    ])->name('admin.exchange_rates.create');

    Route::post('exchange-rates/create', [ExchangeRateController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.exchange_rates.index',
    ])->name('admin.exchange_rates.store');

    Route::get('exchange-rates/edit/{id}', [ExchangeRateController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::settings.exchange_rates.edit',
    ])->name('admin.exchange_rates.edit');

    Route::get('exchange-rates/update-rates', [ExchangeRateController::class, 'updateRates'])->name('admin.exchange_rates.update_rates');

    Route::put('exchange-rates/edit/{id}', [ExchangeRateController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.exchange_rates.index',
    ])->name('admin.exchange_rates.update');

    Route::post('exchange-rates/delete/{id}', [ExchangeRateController::class, 'destroy'])->name('admin.exchange_rates.delete');

    /**
     * Inventory sources routes.
     */
    Route::get('inventory-sources', [InventorySourceController::class, 'index'])->defaults('_config', [
        'view' => 'admin::settings.inventory_sources.index',
    ])->name('admin.inventory_sources.index');

    Route::get('inventory-sources/create', [InventorySourceController::class, 'create'])->defaults('_config', [
        'view' => 'admin::settings.inventory_sources.create',
    ])->name('admin.inventory_sources.create');

    Route::post('inventory-sources/create', [InventorySourceController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.inventory_sources.index',
    ])->name('admin.inventory_sources.store');

    Route::get('inventory-sources/edit/{id}', [InventorySourceController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::settings.inventory_sources.edit',
    ])->name('admin.inventory_sources.edit');

    Route::put('inventory-sources/edit/{id}', [InventorySourceController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.inventory_sources.index',
    ])->name('admin.inventory_sources.update');

    Route::post('inventory-sources/delete/{id}', [InventorySourceController::class, 'destroy'])->name('admin.inventory_sources.delete');

    /**
     * Channels routes.
     */
    Route::get('channels', [ChannelController::class, 'index'])->defaults('_config', [
        'view' => 'admin::settings.channels.index',
    ])->name('admin.channels.index');

    Route::get('channels/create', [ChannelController::class, 'create'])->defaults('_config', [
        'view' => 'admin::settings.channels.create',
    ])->name('admin.channels.create');

    Route::post('channels/create', [ChannelController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.channels.index',
    ])->name('admin.channels.store');

    Route::get('channels/edit/{id}', [ChannelController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::settings.channels.edit',
    ])->name('admin.channels.edit');

    Route::put('channels/edit/{id}', [ChannelController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.channels.index',
    ])->name('admin.channels.update');

    Route::post('channels/delete/{id}', [ChannelController::class, 'destroy'])->name('admin.channels.delete');

    /**
     * Users routes.
     */
    Route::get('users', [UserController::class, 'index'])->defaults('_config', [
        'view' => 'admin::users.users.index',
    ])->name('admin.users.index');

    Route::get('users/create', [UserController::class, 'create'])->defaults('_config', [
        'view' => 'admin::users.users.create',
    ])->name('admin.users.create');

    Route::post('users/create', [UserController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.users.index',
    ])->name('admin.users.store');

    Route::get('users/edit/{id}', [UserController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::users.users.edit',
    ])->name('admin.users.edit');

    Route::put('users/edit/{id}', [UserController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.users.index',
    ])->name('admin.users.update');

    Route::post('users/delete/{id}', [UserController::class, 'destroy'])->name('admin.users.delete');

    Route::get('users/confirm/{id}', [UserController::class, 'confirm'])->defaults('_config', [
        'view' => 'admin::customers.confirm-password',
    ])->name('super.users.confirm');

    Route::post('users/confirm/{id}', [UserController::class, 'destroySelf'])->defaults('_config', [
        'redirect' => 'admin.users.index',
    ])->name('admin.users.destroy');

    /**
     * Slider routes.
     */
    Route::get('sliders', [SliderController::class, 'index'])->defaults('_config', [
        'view' => 'admin::settings.sliders.index',
    ])->name('admin.sliders.index');

    Route::get('sliders/create', [SliderController::class, 'create'])->defaults('_config', [
        'view' => 'admin::settings.sliders.create',
    ])->name('admin.sliders.create');

    Route::post('sliders/create', [SliderController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.sliders.index',
    ])->name('admin.sliders.store');

    Route::get('sliders/edit/{id}', [SliderController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::settings.sliders.edit',
    ])->name('admin.sliders.edit');

    Route::post('sliders/edit/{id}', [SliderController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.sliders.index',
    ])->name('admin.sliders.update');

    Route::post('sliders/delete/{id}', [SliderController::class, 'destroy'])->name('admin.sliders.delete');

    Route::post('sliders/mass-delete', [SliderController::class, 'massDestroy'])->name('admin.sliders.mass_delete');

    /**
     * Tax categories routes.
     */
    Route::get('tax-categories', [TaxCategoryController::class, 'index'])->defaults('_config', [
        'view' => 'admin::tax.tax-categories.index',
    ])->name('admin.tax_categories.index');

    Route::get('tax-categories/create', [TaxCategoryController::class, 'show'])->defaults('_config', [
        'view' => 'admin::tax.tax-categories.create',
    ])->name('admin.tax_categories.create');

    Route::post('tax-categories/create', [TaxCategoryController::class, 'create'])->defaults('_config', [
        'redirect' => 'admin.tax_categories.index',
    ])->name('admin.tax_categories.store');

    Route::get('tax-categories/edit/{id}', [TaxCategoryController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::tax.tax-categories.edit',
    ])->name('admin.tax_categories.edit');

    Route::put('tax-categories/edit/{id}', [TaxCategoryController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.tax_categories.index',
    ])->name('admin.tax_categories.update');

    Route::post('tax-categories/delete/{id}', [TaxCategoryController::class, 'destroy'])->name('admin.tax_categories.delete');

    /**
     * Tax rates routes.
     */
    Route::get('tax-rates', [TaxRateController::class, 'index'])->defaults('_config', [
        'view' => 'admin::tax.tax-rates.index',
    ])->name('admin.tax_rates.index');

    Route::get('tax-rates/create', [TaxRateController::class, 'show'])->defaults('_config', [
        'view' => 'admin::tax.tax-rates.create',
    ])->name('admin.tax_rates.create');

    Route::post('tax-rates/create', [TaxRateController::class, 'create'])->defaults('_config', [
        'redirect' => 'admin.tax_rates.index',
    ])->name('admin.tax_rates.store');

    Route::get('tax-rates/edit/{id}', [TaxRateController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::tax.tax-rates.edit',
    ])->name('admin.tax_rates.edit');

    Route::put('tax-rates/update/{id}', [TaxRateController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.tax_rates.index',
    ])->name('admin.tax_rates.update');

    Route::post('tax-rates/delete/{id}', [TaxRateController::class, 'destroy'])->name('admin.tax_rates.delete');

    Route::post('tax-rates/import', [TaxRateController::class, 'import'])->defaults('_config', [
        'redirect' => 'admin.tax_rates.index',
    ])->name('admin.tax_rates.import');
});
