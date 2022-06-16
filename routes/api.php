<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FollowersController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\PaymentController;
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

    Route::get('auth/posts', [PostsController::class, 'index']);
    Route::get('auth/posts/user', [PostsController::class, 'showProfilUser']);
    Route::get('auth/posts/user/{user_id}', [PostsController::class, 'show']);
    Route::get('auth/post/{id}', [PostsController::class, 'showById']);
    Route::get('auth/search/{key}', [PostsController::class, 'search']);
    Route::post('auth/post', [PostsController::class, 'create']);
    Route::post('auth/post/update/{id}', [PostsController::class, 'updatePost']);

    Route::post('auth/post/comment/{id}', [CommentsController::class, 'create']);
    Route::delete('auth/post/delete/{id}', [PostsController::class, 'delete']);

    Route::delete('auth/post/buy/{id}', [PostsController::class, 'buy']);

    Route::put('auth/post/like/update/{id}', [LikesController::class, 'updateLike']);
    Route::put('auth/user/follower/update/{id}', [FollowersController::class, 'updateFollowers']);

    Route::get('discussion/{id}', [ChatController::class, 'allDiscussion']);
    Route::get('messages', [ChatController::class, 'fetchMessages']);
    Route::post('messages', [ChatController::class, 'sendPrivateMessage']);

    Route::get('auth/users', [UserController::class, 'allUser']);
    Route::get('auth/users/{key}', [UserController::class, 'search']);
    Route::post('auth/update/{id}', [UserController::class, 'update']);
    Route::post('auth/me', [UserController::class, 'me']);
    Route::get('auth/user/{id}', [UserController::class, 'userInfo']);
    Route::get('auth/user/{id}', [UserController::class, 'userInfoPublic']);
    Route::post('auth/logout', [UserController::class, 'logout']);


    Route::post('auth/checkout', [PaymentController::class, 'intent']);
});

Route::post('auth/register', [UserController::class, 'register']);
Route::post('auth/login', [UserController::class, 'login']);
