<?php

namespace App\Tests;

use App\Factory\PizzaFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApiControllerTest extends WebTestCase
{

    use Factories,
        ResetDatabase;

    private function createDataForTest()
    {
        PizzaFactory::createOne([
            'name' => 'My pizza',
            'price' => 200
        ]);

        PizzaFactory::createOne([
            'name' => 'Not my pizza',
            'price' => 400
        ]);
    }

    /**
     * @uses \App\Controller\ApiController::listPizzas()
     */
    public function testListPizzas(): void
    {
        $this->createDataForTest();

        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/list_pizzas');

        $this->assertResponseIsSuccessful();
        $this->assertEquals([
            [
                'name' => 'My pizza',
                'price' => 200
            ],
            [
                'name' => 'Not my pizza',
                'price' => 400
            ]
        ], json_decode($client->getResponse()->getContent(), true));
    }
}
