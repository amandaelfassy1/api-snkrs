<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FollowersController;
use App\Http\Controllers\LikesController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::controller(PostsController::class)->prefix('auth')->group(function () {
        Route::get('posts', 'index');
        Route::get('posts/user', 'showProfilUser');
        Route::get('posts/user/{user_id}', 'show');
        Route::get('post/{id}', 'showById');
        Route::get('search/{key}', 'search');
        Route::post('post', 'create');
    });

    Route::post('auth/post/comment/{id}', [CommentsController::class, 'create']);
    Route::delete('auth/post/delete/{id}', [PostsController::class, 'delete']);

    Route::put('auth/post/like/update/{id}', [LikesController::class, 'updateLike']);

    Route::post('auth/user/followers/{id}', [FollowersController::class, 'followers']);
    Route::get('auth/user/followers/{id}', [FollowersController::class, 'getFollowers']);

    Route::get('auth/user/followers/{id}/count', [FollowersController::class, 'countFollowers']);
    Route::get('auth/user/following/{id}/count', [FollowersController::class, 'countFollowing']);
    Route::put('auth/user/follower/update/{id}', [FollowersController::class, 'updateFollowers']);


    Route::get('messages', [ChatController::class, 'fetchMessages']);
    Route::post('messages', [ChatController::class, 'sendPrivateMessage']);

    Route::post('auth/update/{id}', [UserController::class, 'update']);
    Route::post('auth/me', [UserController::class, 'me']);
    Route::get('auth/user/{id}', [UserController::class, 'userInfo']);
    Route::post('auth/logout', [UserController::class, 'logout']);
});

Route::post('auth/register', [UserController::class, 'register']);
Route::post('auth/login', [UserController::class, 'login']);
