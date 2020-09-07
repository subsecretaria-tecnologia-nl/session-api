<?php

$router->post('/signup','UsersController@signup');
$router->get('/login','AuthController@login');
$router->get('/refresh','AuthController@refresh');

$router->group(['middleware' =>  ['jwt.auth', 'jwt.refresh']], function () use ($router) {
	$router->group(["prefix" => "users"], function() use ($router){
		$router->group(["prefix" => "me"], function() use ($router){
			$router->get('/','UsersController@getUser');
			$router->get('/sessions','UsersController@getSessionUser');
		});
		
		$router->get('/{id}/sessions','SubUsersController@getSessionSubUser');

		$router->post('/','SubUsersController@getSubUsers');
		$router->post('/{id}','SubUsersController@getSubUser');	
		
		$router->put('/me','UsersController@editUser');
		$router->put('/{id}','SubUsersController@editSubUser');
	});

	$router->get('/logout','AuthController@logout');
	$router->post('/signupSubUser','SubUsersController@signupSubUser');
	$router->post('/statusSubUser','SubUsersController@statusSubUser');
});
