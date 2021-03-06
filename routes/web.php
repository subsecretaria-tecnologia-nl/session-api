<?php

$router->group(["middleware" => "json.schema.validation", "prefix" => (getenv("APP_PREFIX") ?? "")], function () use ($router) {
	$router->get('/', function() {
		return "THIS ROUTE DOES NOT EXISTS.";
	});
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
		$router->get("/roles", "NotaryOfficesController@getRoles");
		$router->get('/user','NotaryOfficesController@searchUser');
		$router->get("/{id}", "NotaryOfficesController@getSingle");
		$router->get("/{id}/users", "NotaryOfficesController@getUsers");
		$router->put("/{id}", "NotaryOfficesController@updateNotary");
		$router->post("/{id}/users", "NotaryOfficesController@createUsersNotary");
		$router->post("/{id}/users/{user_id}", "NotaryOfficesController@updateNotaryUsers");
		$router->put("/{id}/users_status/{user_id}", "NotaryOfficesController@statusUser");
		$router->get("/notaryCommunity/{id}", "NotaryOfficesController@getNotaryCommunity");
		$router->put('/notify/{id}/{pass}','NotaryOfficesController@notify');
		$router->get('/file/{id}/{type}','NotaryOfficesController@getFileNotary');


	});
	$router->group(["prefix" => "divisas"], function() use ($router){
		$router->get("/", "DivisasController@getDivisas");
		$router->post("/saveDivisas", "DivisasController@saveDivisas");
		$router->post("/deleteDivisas", "DivisasController@deleteDivisas");
		$router->get("/getDivisasSave", "DivisasController@getDivisasSave");
		$router->post("/getCambioDivisa", "DivisasController@getCambioDivisa");
		
	});
});
