<?php

namespace App\Test\Functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicTest extends WebTestCase
{
      public function testEnvironmentIsOk(): void
      {
            $client = static::createClient();
            $client->request(Request::METHOD_GET, '/');
            $this->assertResponseIsSuccessful();
      }
}
