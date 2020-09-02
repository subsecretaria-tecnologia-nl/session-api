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
$router->post('/login','AuthController@login');
$router->patch('/refresh','AuthController@refresh');



$router->group(['middleware' =>  ['jwt.auth', 'jwt.refresh'], 'prefix'=>'auth'], function () use ($router) {
	$router->post('/logout','AuthController@logout');
	$router->post('/getUser','UsersController@getUser');
	$router->post('/editUser','UsersController@editUser');
	$router->post('/getSessionUser','UsersController@getSessionUser');
	$router->post('/signupSubUser','SubUsersController@signupSubUser');
	$router->post('/getSubUser','SubUsersController@getSubUser');
	$router->post('/editSubUser','SubUsersController@editSubUser');
	$router->post('/statusSubUser','SubUsersController@statusSubUser');
	$router->post('/getSessionSubUser','SubUsersController@getSessionSubUser');









});