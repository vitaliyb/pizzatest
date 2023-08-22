<?php

namespace App\Tests;

use App\Factory\IngredientFactory;
use App\Factory\PizzaFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApiControllerTest extends WebTestCase
{

    use Factories,
        ResetDatabase;


    private function createDataForTest()
    {
        $tomato = IngredientFactory::createOne([
            'name' => 'Tomato'
        ]);

        $cheese = IngredientFactory::createOne([
            'name' => 'Cheese'
        ]);

        $meat = IngredientFactory::createOne([
            'name' => 'Meat'
        ]);

        $myPizza = PizzaFactory::createOne([
            'name' => 'My pizza',
            'price' => 200,
        ])
            ->addIngredient($tomato->object())
            ->addIngredient($cheese->object());

        $notMyPizza = PizzaFactory::createOne([
            'name' => 'Not my pizza',
            'price' => 400
        ])
            ->addIngredient($tomato->object())
            ->addIngredient($meat->object());

        $this->entityManager->persist($myPizza);
        $this->entityManager->flush();
    }


    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    private EntityManager $entityManager;

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
                'price' => 200,
                'ingredients' => [
                    'Tomato', 'Cheese'
                ]
            ],
            [
                'name' => 'Not my pizza',
                'price' => 400,
                'ingredients' => [
                    'Tomato', 'Meat'
                ]
            ]
        ], json_decode($client->getResponse()->getContent(), true));
    }


    /**
     * @uses \App\Controller\ApiController::addPizza()
     */
    public function testItAddsPizza(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $crawler = $client->request('GET', '/api/add_pizza', [
            'name' => 'Brand new pizza',
            'ingredients' => ['Olives', 'Cheese', 'Meat']
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals('ok', json_decode($client->getResponse()->getContent(), true));
    }


    /**
     * @uses \App\Controller\ApiController::addPizza()
     */
    public function testItFailsToAddPizzaWithoutName(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/add_pizza', [
            'ingredients' => ['Olives', 'Cheese', 'Meat']
        ]);

        $this->assertResponseStatusCodeSame(404);
    }


    /**
     * @uses \App\Controller\ApiController::addPizza()
     */
    public function testItFailsToAddPizzaWithoutIngredients(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/add_pizza', [
            'name' => 'Brand new pizza',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }


    /**
     * @uses \App\Controller\ApiController::addPizza()
     */
    public function testItFailsToAddPizzaWithoutParameters(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/add_pizza');

        $this->assertResponseStatusCodeSame(404);
    }
}