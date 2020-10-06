<?php

$router->group(["middleware" => "json.schema.validation"], function () use ($router) {
	$router->post('/signup','UsersController@signup');
	$router->get('/login','AuthController@login');
	$router->get('/refresh','AuthController@refresh');
	$router->post('/password/recovery', 'PasswordController@postEmail');
	$router->post('/password/recovery/{token}', [ 'as' => 'password.reset', 'uses' => 'PasswordController@postReset']);
	$router->get('/password/recovery/{token}', 'PasswordController@validateToken');

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

	$router->group(["prefix" => "notary-offices"], function() use ($router){
		$router->post("/", "NotaryOfficesController@signup");
		$router->get("/", "NotaryOfficesController@getMany");
		$router->get("/{id}", "NotaryOfficesController@getSingle");
		$router->get("/{id}/users", "NotaryOfficesController@getUsers");
		$router->put("/{id}", "NotaryOfficesController@updateNotary");
		$router->post("/{id}/users", "NotaryOfficesController@createUsersNotary");
		$router->put("/{id}/users/{user_id}", "NotaryOfficesController@updateNotaryUsers");

	});
});