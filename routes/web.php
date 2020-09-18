<?php

$router->post('/signup','UsersController@signup');
$router->get('/login','AuthController@login');
$router->get('/refresh','AuthController@refresh');
$router->post('/password/recovery', 'PasswordController@postEmail');
$router->post('/password/recovery/token', [ 'as' => 'password.reset', 'uses' => 'PasswordController@postReset']);
$router->get('/password/recovery/token', 'PasswordController@validateToken');

$router->group(['middleware' =>  ['jwt.auth', 'jwt.refresh']], function () use ($router) {

	// Group with prefix "USERS" => {{APP_HOSTNAME}}/users/[...]
	$router->group(["prefix" => "users"], function() use ($router){

		// Group with prefix "ME" => {{APP_HOSTNAME}}/users/me/[...]
		$router->group(["prefix" => "me"], function() use ($router){
			$router->get('/','UsersController@getUser');
			$router->put('/','UsersController@editUser');
			$router->get('/sessions','UsersController@getSessionUser');
		});
		
		// These routes also use the USERS prefix
		$router->get('/{id}/sessions','UsersController@getSessionSubUser');
		$router->get('/','UsersController@getSubUsers');
		$router->post('/{id}','UsersController@getSubUser');	
		$router->put('/{id}','UsersController@editSubUser');
	});

	// Theses routes has not route prefix. But use JWT middlewares
	$router->get('/logout','AuthController@logout');
	$router->post('/signupSubUser','UsersController@signupSubUser');
	$router->post('/statusSubUser','UsersController@statusSubUser');
});
