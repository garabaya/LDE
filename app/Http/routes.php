<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::auth();
    Route::get('/', 'HomeController@index');
    Route::get('/index', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::get('/com/{id}', 'CommunityController@show')->where('id', '[0-9]+');
    Route::get('/com/create', 'CommunityController@create');
    Route::get('/com/{id}/createInitiative', 'CommunityController@createInitiative')->where('id', '[0-9]+');
    Route::post('/com', 'CommunityController@store');
    Route::post('/com/join', 'CommunityController@join');
    Route::get('/metainitiative/{id}', 'MetainitiativeController@show')->where('id', '[0-9]+');
    Route::post('/metainitiative/support', 'MetainitiativeController@support');
    Route::get('/metainitiative/create', 'MetainitiativeController@create');
    Route::post('/metainitiative','MetainitiativeController@store');
    Route::post('/metainitiative/ruleSelected','MetainitiativeController@ruleSelected');
    Route::post('/thread', 'ThreadController@comment');
    Route::get('/initiative/create', 'InitiativeController@create');
    Route::post('/initiative','InitiativeController@store');
    Route::get('/initiative/{id}', 'InitiativeController@show')->where('id', '[0-9]+');
    Route::post('/initiative/support', 'InitiativeController@support');
    Route::get('/user/{wrapper_id}', 'UserController@show')->where('wrapper_id', '[0-9]+');
    Route::get('/user', 'UserController@show');



});
