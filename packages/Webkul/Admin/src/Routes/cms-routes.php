<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\CMS\PageController;

/**
 * CMS routes.
 */
Route::group(['middleware' => ['admin'], 'prefix' => config('app.admin_url')], function () {
    Route::prefix('cms')->group(function () {
        Route::get('/', [PageController::class, 'index'])->defaults('_config', [
            'view' => 'admin::cms.index',
        ])->name('admin.cms.index');

        Route::get('create', [PageController::class, 'create'])->defaults('_config', [
            'view' => 'admin::cms.create',
        ])->name('admin.cms.create');

        Route::post('create', [PageController::class, 'store'])->defaults('_config', [
            'redirect' => 'admin.cms.index',
        ])->name('admin.cms.store');

        Route::get('edit/{id}', [PageController::class, 'edit'])->defaults('_config', [
            'view' => 'admin::cms.edit',
        ])->name('admin.cms.edit');

        Route::post('edit/{id}', [PageController::class, 'update'])->defaults('_config', [
            'redirect' => 'admin.cms.index',
        ])->name('admin.cms.update');

        Route::post('delete/{id}', [PageController::class, 'delete'])->defaults('_config', [
            'redirect' => 'admin.cms.index',
        ])->name('admin.cms.delete');

        Route::post('mass-delete', [PageController::class, 'massDelete'])->defaults('_config', [
            'redirect' => 'admin.cms.index',
        ])->name('admin.cms.mass_delete');
    });
});
