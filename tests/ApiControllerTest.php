<?php

namespace App\Tests;

use App\Factory\IngredientFactory;
use App\Factory\PizzaFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApiControllerTest extends WebTestCase
{

    use Factories,
        ResetDatabase;


    private function createDataForTest()
    {
        $tomato = IngredientFactory::createOne([
            'name' => 'Tomato',
            'price' => 2.50
        ]);

        $cheese = IngredientFactory::createOne([
            'name' => 'Cheese',
            'price' => 5
        ]);

        $meat = IngredientFactory::createOne([
            'name' => 'Meat',
            'price' => 10
        ]);

        $myPizza = PizzaFactory::createOne([
            'name' => 'My pizza',
        ])
            ->addIngredient($tomato->object())
            ->addIngredient($cheese->object());

        $notMyPizza = PizzaFactory::createOne([
            'name' => 'Not my pizza'
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
                'price' => 7.50,
                'ingredients' => [
                    'Tomato', 'Cheese'
                ]
            ],
            [
                'name' => 'Not my pizza',
                'price' => 12.50,
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
            'ingredients' => [
                ['name' => 'Olives', 'price' => 2.50],
                ['name' => 'Cheese', 'price' => 5],
                // TODO: ResetDatabase is not working? I had to change Meat to Meat balls to avoid price conflicts
                ['name' => 'Meat balls', 'price' => 7]
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals([
            'name' => 'Brand new pizza',
            'ingredients' => [
                'Olives',
                'Cheese',
                'Meat balls'
            ],
            'price' => 14.50
        ], json_decode($client->getResponse()->getContent(), true));
    }


    /**
     * @uses \App\Controller\ApiController::addPizza()
     */
    public function testItFailsToAddPizzaWithoutName(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $crawler = $client->request('GET', '/api/add_pizza', [
            'ingredients' => [
                ['name' => 'Olives', 'price' => 2.50],
                ['name' => 'Cheese', 'price' => 5],
                ['name' => 'Meat', 'price' => 7]
            ]
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
