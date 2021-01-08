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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->namespace('Api')->name('api.v1.')
    ->middleware('change-locale')
    ->group(function () {
        Route::middleware('throttle:' . config('api.rate_limits.sign'))->group(function () {
            // 图片验证码
            Route::post('captchas', 'CaptchasController@store')->name('captchas.store');

            // 短信验证码
            Route::post('verificationCodes', 'VerificationCodesController@store')->name('verificationCodes.store');

            // 用户注册
            Route::post('users', 'UsersController@store')->name('users.store');

            // 第三方登录
            Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')->where('social_type', 'wechat')->name('socials.authorizations.store');

            // 登录
            Route::post('authorizations', 'AuthorizationsController@store')->name('authorizations.store');

            // 小程序登录
            Route::post('weapp/authorizations', 'AuthorizationsController@weappStore')->name('weapp.authorizations.store');

            // 小程序注册
            Route::post('weapp/users', 'UsersController@weappStore')->name('weapp.users.store');

            // 刷新 Token
            Route::put('authorizations/current', 'AuthorizationsController@update')->name('authorizations.update');
            // 删除 Token
            Route::delete('authorizations/current', 'AuthorizationsController@destroy')->name('authorizations.destroy');
        });

        Route::middleware('throttle:' . config('api.rate_limits.access'))->group(function () {
            // 游客可以访问的接口

            Route::get('users/{user}', 'UsersController@show')->name('users.show');        // 某个用户的详情
        Route::get('categories', 'CategoriesController@index')->name('categories.index'); // 分类列表

        Route::get('users/{user}/topics', 'TopicsController@userIndex')->name('users.topics.index'); // 某个用户发布话题

            Route::resource('topics', 'TopicsController')->only(['index', 'show']); // 话题列表，详情

            Route::get('links', 'LinksController@index')->name('links.index'); // 资源推荐
        Route::get('actived/users', 'UsersController@activedIndex')->name('actived.users.index'); // 活跃用户



        // 登录以后可以访问的接口
            Route::middleware('auth:api')->group(function () {
                Route::get('user', 'UsersController@me')->name('user.show');

                Route::post('images', 'ImagesController@store')->name('images.store');


                Route::patch('user', 'UsersController@update')->name('user.update'); // 编辑用户信息
                Route::put('user', 'UsersController@update')->name('user.update'); // 编辑用户信息

                Route::post('images', 'ImagesController@store')->name('images.store'); // 上传图片

                Route::resource('topics', 'TopicsController')->only(['store', 'update', 'destroy']); // 发布话题

                Route::post('topics/{topic}/replies', 'RepliesController@store')->name('topic.replies.store'); // 发布回复
            Route::delete('topics/{topic}/replies/{reply}', 'RepliesController@destroy')->name('topic.replies.destroy'); // 删除回复
            Route::get('topics/{topic}/replies', 'RepliesController@index')->name('topics.replies.index'); // 话题回复列表
            Route::get('notifications', 'NotificationsController@index')->name('notifications.index'); // 通知列表
            Route::get('notifications/stats', 'NotificationsController@stats')->name('notifications.stats'); // 通知统计
            Route::patch('user/read/notifications', 'NotificationsController@read')->name('user.notifications.read'); // 标记消息通知已读

            Route::get('user/permissions', 'PermissionsController@index')->name('user.permissions.index'); // 当前登录用户的权限
            });
        });
    });
