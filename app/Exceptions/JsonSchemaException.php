<?php 
namespace App\Exceptions;

class JsonSchemaException extends \Exception {
	protected $code;
	protected $message;

	public function __construct($description=NULL){
		$this->description = isset($description) ? $description : $this->getDescription();
		$this->code = 422;
		$this->message = "JSON Schema Exception";
	}

	public function toArray() {
		$data = array(
			"code" => $this->code,
			"description" => $this->getDescription(),
			"message" => $this->message,
		);
	}

	final public function getDescription(){
		if(isset($this->description)) return $this->description;
		else return NULL;
	}

}