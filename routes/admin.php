<?php

use App\Http\Controllers;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:is-admin'])->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    })->name('index');

    Route::resource('users', Controllers\Admin\UserController::class);
    Route::get('/users-index.json', [Controllers\Admin\UserController::class, 'indexData'])->name('users.index.data');
    Route::delete('/users-bulk-destroy', [Controllers\Admin\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::put('/users-bulk-edit', [Controllers\Admin\UserController::class, 'bulkUpdate'])->name('users.bulk-edit');
    Route::get('/users/{user}/password', [Controllers\Admin\UserController::class, 'editPassword'])->name('users.password.edit');
    Route::put('/users/{user}/password', [Controllers\Admin\UserController::class, 'updatePassword'])->name('users.password.update');

    Route::resource('groups', Controllers\Admin\GroupController::class);
    Route::get('/groups-index.json', [Controllers\Admin\GroupController::class, 'indexData'])->name('groups.index.data');

    Route::resource('admins', Controllers\Admin\AdminController::class)->parameters([
        'admins' => 'group',
    ]);
    Route::get('/admins-index.json', [Controllers\Admin\AdminController::class, 'indexData'])->name('admins.index.data');

    Route::get('/groups/{group}/member-permissions', [Controllers\Admin\PermissionController::class, 'editMemberPermissions'])->name('groups.member-permissions.edit');
    Route::put('/groups/{group}/member-permissions', [Controllers\Admin\PermissionController::class, 'updateMemberPermissions'])->name('groups.member-permissions.update');
    Route::get('/groups/{group}/admin-permissions', [Controllers\Admin\PermissionController::class, 'editAdminPermissions'])->name('groups.admin-permissions.edit');
    Route::put('/groups/{group}/admin-permissions', [Controllers\Admin\PermissionController::class, 'updateAdminPermissions'])->name('groups.admin-permissions.update');

    Route::resource('pages', Controllers\Admin\PageController::class);
    Route::get('/pages-index.json', [Controllers\Admin\PageController::class, 'indexData'])->name('pages.index.data');
    Route::delete('/pages-bulk-destroy', [Controllers\Admin\PageController::class, 'bulkDestroy'])->name('pages.bulk-destroy');

    Route::resource('posts', Controllers\Admin\PostController::class);
    Route::get('/posts-index.json', [Controllers\Admin\PostController::class, 'indexData'])->name('posts.index.data');
    Route::delete('/posts-bulk-destroy', [Controllers\Admin\PostController::class, 'bulkDestroy'])->name('posts.bulk-destroy');

    Route::resource('contacts', Controllers\Admin\ContactController::class)->only(['index', 'show', 'destroy']);
    Route::get('/contacts-index.json', [Controllers\Admin\ContactController::class, 'indexData'])->name('contacts.index.data');
    Route::put('/contacts/{contact}/toggle-status', [Controllers\Admin\ContactController::class, 'toggleStatus'])->name('contacts.toggle-status');
    Route::delete('/contacts-bulk-destroy', [Controllers\Admin\ContactController::class, 'bulkDestroy'])->name('contacts.bulk-destroy');
    Route::put('/contacts-bulk-read', [Controllers\Admin\ContactController::class, 'bulkMarkAsRead'])->name('contacts.bulk-read');

    Route::prefix('/settings')->name('settings.')->group(function () {
        Route::get('/general', [Controllers\Admin\Settings\GeneralSettingsController::class, 'edit'])->name('general');
        Route::put('/general', [Controllers\Admin\Settings\GeneralSettingsController::class, 'update']);

        Route::get('/sitemap', [Controllers\Admin\Settings\SitemapSettingsController::class, 'edit'])->name('sitemap');
        Route::put('/sitemap', [Controllers\Admin\Settings\SitemapSettingsController::class, 'update']);

        Route::resource('widgets', Controllers\Admin\Settings\WidgetsController::class);

        Route::prefix('/pages')->name('pages.')->group(function () {
            Route::get('/home', [Controllers\Admin\Settings\Pages\HomeController::class, 'edit'])->name('home');
            Route::put('/home', [Controllers\Admin\Settings\Pages\HomeController::class, 'update']);

            Route::get('/404', [Controllers\Admin\Settings\Pages\FallbackController::class, 'edit'])->name('fallback');
            Route::put('/404', [Controllers\Admin\Settings\Pages\FallbackController::class, 'update']);
        });
    });
});