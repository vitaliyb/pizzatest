<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Pizza;
use App\Entity\PizzaIngredient;
use App\Repository\PizzaRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/list_pizzas', name: 'api.list_pizzas')]
    public function listPizzas(PizzaRepository $pizzaRepository): JsonResponse
    {
        $pizzas = $pizzaRepository->findAll();
        return $this->json(
            array_map(function (Pizza $pizza) {
                $ingredients = array_map(function (PizzaIngredient $pizzaIngredient) {
                    return $pizzaIngredient->getIngredientId()->getName();
                }, $pizza->getPizzaIngredients()->toArray());

                return [
                    'name' => $pizza->getName(),
                    'price' => $pizza->getPrice(),
                    'ingredients' => $ingredients
                ];
            }, $pizzas)
        );
    }

    #[Route('/api/add_pizza', name: 'api.add_pizza')]
    public function addPizza(
        #[MapQueryParameter] string $name,
        #[MapQueryParameter] array  $ingredients,
        Request                     $request,
        EntityManagerInterface      $entityManager
    ): JsonResponse
    {
        $pizza = new Pizza();
        $pizza->setName($name);

        foreach ($ingredients as $ingredientProperties) {
            $ingredientName = $ingredientProperties['name'];
            $ingredientPrice = $ingredientProperties['price'];

            $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['name' => $ingredientName]);
            if (!$ingredient) {
                $ingredient = new Ingredient();
                $ingredient->setName($ingredientName);
                $ingredient->setPrice($ingredientPrice);

                $entityManager->persist($ingredient);
            }

            $pizza->addIngredient($ingredient);
        }

        $entityManager->persist($pizza);

        return $this->json('ok');
    }

    #[Route('/api/add_ingredient', name: 'api.add_ingredient')]
    public function addIngredient()
    {
        // TODO: write
    }

    #[Route('/api/remove_ingredient', name: 'api.remove_ingredient')]
    public function removeIngredient()
    {
        // TODO: write
    }
}
