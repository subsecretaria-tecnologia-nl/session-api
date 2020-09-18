<?php
namespace App\Schemas;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\InvalidValue;
use App\Exceptions\JsonSchemaException;

class SchemaValidator{
	public static function validateJson(&$json, &$schema){
		try{
			if(is_string($schema))
				$schemaVal = Schema::import(json_decode($schema));
			else if($schema instanceof Schema)
				$schemaVal = $schema;
			else
				throw new JsonSchemaException("Unknown schema format. Should be json string or Swaggest\JsonSchema\Schema");

			$schemaVal->in($json);
		} catch (InvalidValue $e) {
			$message = explode(", data: ", $e->getMessage())[0];
			$message = str_replace("properties:", "", $message);
			$message = str_replace("\$ref[#/definitions/", "[", $message);

			if($message == "Object, boolean expected, null received")
				throw new JsonSchemaException("Unknown schema format. There is a format error in JSON Schema File.");
			throw new JsonSchemaException($message);
		}
	}
}