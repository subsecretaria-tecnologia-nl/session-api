<?php

$router->post('/signup','UsersController@signup');
$router->get('/login','AuthController@login');
$router->get('/refresh','AuthController@refresh');

$router->group(['middleware' =>  ['jwt.auth', 'jwt.refresh']], function () use ($router) {
	$router->group(["prefix" => "users"], function() use ($router){
		$router->get('/me','UsersController@getUser');
		$router->get('/me/sessions','UsersController@getSessionUser');
		$router->get('/user/{id}/sessions','SubUsersController@getSessionSubUser');

		$router->post('/users','SubUsersController@getSubUsers');
		$router->post('/users/{id}','SubUsersController@getSubUser');	
		
		$router->put('/me','UsersController@editUser');
		$router->put('/users/{id}','SubUsersController@editSubUser');
	});

	$router->get('/logout','AuthController@logout');
	$router->post('/signupSubUser','SubUsersController@signupSubUser');
	$router->post('/statusSubUser','SubUsersController@statusSubUser');
});
