<?php 
namespace App\Exceptions;

class ShowableException extends \Exception {
	protected $code;
	protected $message;

	public function __construct($code=NULL, $message=NULL, $description=NULL){
		$this->description = isset($description) ? $description : $this->getDescription();
		$this->code = isset($code) ? $code : $this->getCode();
		$this->message = isset($message) ? $message : $this->getMessage();
	}

	public function toArray() {
		$data = array(
			"code" => $this->code,
			"description" => $this->getDescription(),
			"message" => $this->message
		);
	}

	final public function getDescription(){
		if(isset($this->description)) return $this->description;
		else return NULL;
	}

}