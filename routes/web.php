<?php

$router->post('/signup','UsersController@signup');
$router->get('/login','AuthController@login');
$router->get('/refresh','AuthController@refresh');

$router->group(['middleware' =>  ['jwt.auth', 'jwt.refresh']], function () use ($router) {

	// Group with prefix "USERS" => {{APP_HOSTNAME}}/users/[...]
	$router->group(["prefix" => "users"], function() use ($router){

		// Group with prefix "ME" => {{APP_HOSTNAME}}/users/me/[...]
		$router->group(["prefix" => "me"], function() use ($router){
			$router->get('/','UsersController@getUser');
			$router->get('/sessions','UsersController@getSessionUser');
			$router->put('/','UsersController@editUser');
		});
		
		// These routes also use the USERS prefix
		$router->get('/{id}/sessions','SubUsersController@getSessionSubUser');
		$router->post('/','SubUsersController@getSubUsers');
		$router->post('/{id}','SubUsersController@getSubUser');	
		$router->put('/{id}','SubUsersController@editSubUser');
	});

	// Theses routes has not route prefix. But use JWT middlewares
	$router->get('/logout','AuthController@logout');
	$router->post('/signupSubUser','SubUsersController@signupSubUser');
	$router->post('/statusSubUser','SubUsersController@statusSubUser');
});
