<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthenticationTest extends TestCase
{
	/**
	* A basic test example.
	*
	* @return void
	*/
	public function testLoginNoBasic() {
		$response = $this->json('get', '/login')->response;

		$this->assertEquals(401, $response->getStatusCode());
		$this->assertEquals("error", $response->original->data);
		$this->assertEquals("Unauthorized", $response->original->error->message);
		$this->assertEquals("Basic Authorization Headers required.", $response->original->error->description);
	}
}
