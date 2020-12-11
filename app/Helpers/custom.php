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
if (!function_exists('public_path')) {
	/**
	 * Get the path to the public folder.
	 *
	 * @param  string $path
	 * @return string
	 */
	 function public_path($path = '')
	 {
		 return env('PUBLIC_PATH', base_path('public')) . ($path ? '/' . $path : $path);
	 }
 }
