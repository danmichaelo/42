<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testHome()
	{
		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
	}
    
    /**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testSubjectsIndex()
	{
		$crawler = $this->action('GET', 'SubjectController@index');

        $this->assertTrue($this->client->getResponse()->isOk());
    }

}
