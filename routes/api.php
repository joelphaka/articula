<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->group(function () {
    // Authenticated user
    Route::get('/user', 'Api\UserController@index');
    Route::put('/user', 'Api\UserController@update');
    Route::post('/logout', 'Api\AuthController@logout');

    // Articles
    Route::get('/articles', 'Api\ArticleController@index');
    Route::post('/articles', 'Api\ArticleController@store');
    Route::get('/articles/{article}', 'Api\ArticleController@show');
    Route::post('/articles/{article}', 'Api\ArticleController@update');
    Route::delete('/articles/{article}', 'Api\ArticleController@destroy');

    Route::post('/articles/view/{article}', 'Api\ArticleController@incrementViewCount');

    // Likes
    Route::post('/likes/like/article/{article}', 'Api\LikeController@likeArticle');
    Route::post('/likes/unlike/article/{article}', 'Api\LikeController@unlikeArticle');
    Route::post('/likes/like/comment/{comment}', 'Api\LikeController@likeComment');
    Route::post('/likes/unlike/comment/{comment}', 'Api\LikeController@unlikeComment');

    // Profile
    Route::get('/profile/{user}', 'Api\ProfileController@index');
    Route::get('/timeline/articles/{user}/', 'Api\ProfileController@articlesTimeline');

    // Search
    Route::get('/search/people', 'Api\SearchController@searchUsers');
    Route::get('/search/articles', 'Api\SearchController@searchArticles');

    // Avatar
    Route::post('/avatars', 'Api\AvatarController@store');
    Route::delete('/avatars', 'Api\AvatarController@destroy');

    // Comments
    Route::get('/comments', 'Api\CommentController@index');
    Route::get('/comments/{comment}', 'Api\CommentController@show');
    Route::post('/comments/{article}', 'Api\CommentController@store');
    Route::put('/comments/{comment}', 'Api\CommentController@update');
    Route::delete('/comments/{comment}', 'Api\CommentController@destroy');
    Route::get('/comments/article/{article}', 'Api\CommentController@getArticleComments');
    Route::get('/comments/replies/{comment}','Api\CommentController@getReplies');
    Route::post('/comments/reply/{comment}', 'Api\CommentController@replyToComment');

});

// Auth
Route::post('/login', 'Api\AuthController@login');
Route::post('/register', 'Api\AuthController@register');

