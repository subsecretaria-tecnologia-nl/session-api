<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

		
// $router->get('/', function () use ($router) {
	// return $router->app->version();
// });
$router->post('/signup','UsersController@signup');
$router->get('/login','AuthController@login');
$router->get('/refresh','AuthController@refresh');



$router->group(['middleware' =>  ['jwt.auth', 'jwt.refresh'], 'prefix'=>'auth'], function () use ($router) {
	$router->get('/logout','AuthController@logout');
	$router->get('/user/me','UsersController@getUser');
	$router->put('/user/me','UsersController@editUser');
	$router->get('/user/me/sessions','UsersController@getSessionUser');
	$router->post('/signupSubUser','SubUsersController@signupSubUser');
	$router->post('/users','SubUsersController@getSubUsers');
	$router->put('/users/{id}','SubUsersController@editSubUser');
	$router->post('/users/{id}','SubUsersController@getSubUser');	
	$router->post('/statusSubUser','SubUsersController@statusSubUser');
	$router->get('/user/{id}/sessions','SubUsersController@getSessionSubUser');



});