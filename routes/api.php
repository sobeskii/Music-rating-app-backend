<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\LikedRatingsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/auth', [AuthController::class, 'redirect']);
//Admin, User, Guest routes
Route::group([], function () {
    Route::get('/release/charts', [ReleaseController::class, 'show_chart'])->name('release.charts');
    Route::get('/release/{spotifyId}', [ReleaseController::class, 'show'])->name('release.show');
    Route::get('/artist/{spotifyId}', [ArtistController::class, 'show'])->name('artist.show');
    Route::get('/search', [SearchController::class, 'show'])->name('search.show');
    Route::get('/user/{user}', [UserController::class, 'show_profile'])->name('user.profile');
    Route::get('/user/{user}/reactions', [UserController::class, 'show_reactions'])->name('user.reactions');
    Route::get('/user/{user}/ratings', [UserController::class, 'show_ratings'])->name('user.ratings');
    Route::get('/user/{user}/top_artists', [UserController::class, 'show_top_artists'])->name('user.top_artists');
});
Route::group(['middleware' => ['auth:api', 'role']], function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user['role'] = $user->role;
        return $user;
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh_token', [AuthController::class, 'userRefreshToken']);

    //Admin, User routes
    Route::group(['middleware' => ['scope:admin,user']], function () {
        Route::get('discover_artists',[UserController::class,'discover_artists'])->name('user.discover');
        Route::post('release/rating/{rating}/toggle/', [LikedRatingsController::class, 'toggle'])->name('like');
        Route::put('release/rating/put', [RatingController::class, 'put'])->name('rating.put');
        Route::delete('release/rating/{rating}/delete', [RatingController::class, 'delete'])->name('rating.delete');
    });
    //Admin routes
    Route::group(['middleware' => ['scope:admin']], function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/flagged',[AdminController::class,'flagged_reviews'])->name('admin.flagged');
        Route::get('admin/rules',[AdminController::class,'rules'])->name('admin.rules');
        Route::post('/admin/ban', [AdminController::class, 'ban_user'])->name('admin.ban');
        Route::post('/admin/unban', [AdminController::class, 'unban_user'])->name('admin.unban');
        Route::post('/admin/mute', [AdminController::class, 'mute_user'])->name('admin.mute');
        Route::post('/admin/unmute', [AdminController::class, 'unmute_user'])->name('admin.unmute');
        Route::post('/admin/review_revert',[AdminController::class,'revert_flag'])->name('admin.review_revert');
        Route::post('/admin/rules/create',[AdminController::class,'create_rule'])->name('admin.rules.create');
        Route::put('/admin/rules/{rule}/edit', [AdminController::class,'edit_rule'])->name('admin.rules.edit');
        Route::delete('/admin/rules/{rule}/delete',[AdminController::class,'delete_rule'])->name('admin.rules.delete');
    });
});
