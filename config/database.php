<?php

$db = [
	"default" => "",
	"connections" => []
];

foreach($_ENV as $var => $val){
	preg_match("/^DB_CONNECTION(_[A-Z]+)?$/", $var, $matches, PREG_OFFSET_CAPTURE);
	if(!empty($matches)){
		if(!empty($matches[1])){
			$conname = strtolower($matches[1][0]);
		}

		if(empty($db["connections"]))
			$db["default"] = "db".($conname ?? "");

		extract(parse_url(env($var)));
		$db["connections"]["db".($conname ?? "")] = [
			"driver" => $scheme,
			"host" => $host,
			"database" => str_replace("/", "", $path),
			"username" => $user,
			"password" => $pass,
			"charset"   => 'utf8',
			"collation" => 'utf8_unicode_ci',
		];
	}
}

return $db;