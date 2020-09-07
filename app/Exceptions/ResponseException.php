<?php

namespace App\Exceptions;
use Illuminate\Http\Request;

class ResponseException{
	public $data;
	public $action;
	public $method;
	public $error;
	public $response;

	function __construct($type, $data){
		$request = Request();
		$action = $request->getPathInfo();
		$method = $request->getMethod();

		$this->data = $type;
		$this->action = $action;
		$this->method = $method;
		$this->$type = $data;

		unset($this->{$type == "error" ? "response" : "error"});
		if(env("APP_ENV") == "production"){
			unset($this->action);
			unset($this->method);
		}
	}
}