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

Route::group(['namespace'=>'Auth'], function(){
    Route::post('register-front-end-user','AuthenticationController@registerFrontEndUser');
    Route::post('login-user','AuthenticationController@loginUserAdmin');
    Route::post('register-admin','AuthenticationController@registerAdmin');
});
Route::group(['middleware'=>'auth:api'], function() {
   Route::get('get-master-list/{type}/{id?}','MasterController@getMasterData');
   Route::get('get-users-list','UserController@getFrontEndUserList');
   Route::group(['namespace'=>'Navbar'], function(){
       Route::post('add-navbar','NavbarController@addNavbar');
       Route::get('navbar-list','NavbarController@getNavbarList');
       Route::get('navbar-data/{id}','NavbarController@getNavbarData');
       Route::get('delete-navbar/{id}','NavbarController@deleteNavbar');
       
       Route::post('add-subnavbar','SubnavbarController@addSubNavbar');
       Route::get('subnavbar-list/{id}','SubnavbarController@getSubNavbarList');
       Route::get('subnavbar-data/{id}','SubnavbarController@getSubNavbarData');
       Route::get('delete-subnavbar/{id}','SubnavbarController@deleteSubNavbar');
   });
   Route::group(['namespace'=>'StaticPage'], function(){
       Route::post('add-static-page','StaticPageController@addStaticPage');
       Route::get('static-page-list','StaticPageController@getStaticPageList');
       Route::get('static-page-data/{id}','StaticPageController@getStaticPageData');
       Route::get('delete-static-page/{id}','StaticPageController@deleteStaticPage');
   });
   Route::group(['namespace'=>'News'], function(){
       Route::post('add-news','NewsController@addNews');
       Route::get('news-list','NewsController@getNewsList');
       Route::get('news-data/{id}','NewsController@getNewsData');
       Route::get('delete-news/{id}','NewsController@deleteNews');
   });
});