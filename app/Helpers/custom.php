<?php

if(!function_exists("to_object")){
	function to_object ($arr){
		return json_decode(json_encode($arr));
	}
}

if(!function_exists("to_array")){
	function to_array ($arr){
		return json_decode(json_encode($arr), true);
	}
}
