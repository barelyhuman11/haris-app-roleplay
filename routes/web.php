<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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

Route::get('/', [Controllers\Main\HomeController::class, 'index'])->name('index');
Route::get('/post/{post}', [Controllers\Main\Content\PostController::class, 'index'])->name('post');
Route::get('/search', [Controllers\Main\Content\SearchController::class, 'index'])->name('search');

Route::get('/p/{page}', [Controllers\Main\Misc\PageController::class, 'page'])->name('page');
Route::get('/contact', [Controllers\Main\Misc\ContactController::class, 'contact'])->name('contact');
Route::post('/contact', [Controllers\Main\Misc\ContactController::class, 'send']);

Route::get('/sitemaps-index.xml', [Controllers\Main\Misc\SitemapController::class, 'index'])->name('sitemaps');
Route::get('/sitemap-posts-{index}.xml', [Controllers\Main\Misc\SitemapController::class, 'sitemapPosts'])->name('sitemap.posts');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

Route::prefix('settings')->name('account.account-settings.')->middleware(['auth'])->group(function () {
    Route::get('/', [Controllers\Main\Account\AccountSettingsController::class, 'index'])->name('index');

    Route::get('/username', [Controllers\Main\Account\UsernameController::class, 'edit'])->name('username.edit');
    Route::put('/username', [Controllers\Main\Account\UsernameController::class, 'update'])->name('username.update');

    Route::get('/email', [Controllers\Main\Account\EmailController::class, 'edit'])->name('email.edit');
    Route::put('/email', [Controllers\Main\Account\EmailController::class, 'update'])->name('email.update');

    Route::get('/password', [Controllers\Main\Account\PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [Controllers\Main\Account\PasswordController::class, 'update'])->name('password.update');

    Route::get('/avatar', [Controllers\Main\Account\AvatarController::class, 'edit'])->name('avatar.edit');
    Route::get('/avatar/crop', [Controllers\Main\Account\AvatarController::class, 'edit']);
    
    Route::post('/avatar/crop', [Controllers\Main\Account\AvatarController::class, 'crop'])->name('avatar.crop');
    Route::post('/avatar', [Controllers\Main\Account\AvatarController::class, 'update'])->name('avatar.update');
});
