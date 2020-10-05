<?php


namespace Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameControllerTest extends WebTestCase
{
    public function testCreateGame()
    {
        $client = static::createClient();

        $data = ['name'=>'Test Battle'];

        $client->request(Request::METHOD_POST, "/games", [],[],['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    public function testListGames()
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, "/games", [],[],['CONTENT_TYPE' => 'application/json']);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}