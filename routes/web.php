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

$router->group(['middleware' =>  ['jwt.auth', 'jwt.refresh']], function () use ($router) {
	$router->post('/auth/logout','AuthController@logout');
	$router->patch('/auth/refresh','AuthController@refresh');

});