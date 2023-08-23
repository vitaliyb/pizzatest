<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Pizza;
use App\Entity\PizzaIngredient;
use App\Repository\PizzaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
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
                return $this->pizzaToArray($pizza);
            }, $pizzas)
        );
    }

    private function pizzaToArray(Pizza $pizza): array
    {
        // TODO: removing pizza ingredients affects array index
        $ingredients = $pizza->getPizzaIngredients()->toArray();
        $ingredients = array_values($ingredients);

        $ingredientsNames = array_map(function (PizzaIngredient $pizzaIngredient) {
            return $pizzaIngredient->getIngredientId()->getName();
        }, $ingredients);

        return [
            'name' => $pizza->getName(),
            'price' => $pizza->getPriceForDisplay(),
            'ingredients' => $ingredientsNames
        ];
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
            $this->addIngredientToPizza($entityManager, $pizza, $ingredientProperties['name'], $ingredientProperties['price']);
        }

        $entityManager->persist($pizza);

        return $this->json($this->pizzaToArray($pizza));
    }

    private function addIngredientToPizza(EntityManagerInterface $entityManager, Pizza $pizza, $ingredientName, $ingredientPrice)
    {
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['name' => $ingredientName]);
        if (!$ingredient) {
            $ingredient = new Ingredient();
            $ingredient->setName($ingredientName);
            $ingredient->setPrice($ingredientPrice);

            $entityManager->persist($ingredient);
        }

        $pizza->addIngredient($ingredient);
    }

    #[Route('/api/add_ingredient', name: 'api.add_ingredient')]
    public function addIngredient(
        #[MapQueryParameter] int    $pizza_id,
        #[MapQueryParameter] string $ingredient_name,
        #[MapQueryParameter] float  $ingredient_price,
        Request                     $request,
        EntityManagerInterface      $entityManager
    ): JsonResponse
    {
        $pizza = $entityManager->getRepository(Pizza::class)->find($pizza_id);

        $this->addIngredientToPizza($entityManager, $pizza, $ingredient_name, $ingredient_price);

        $entityManager->persist($pizza);

        return $this->json($this->pizzaToArray($pizza));
    }

    #[Route('/api/remove_ingredient', name: 'api.remove_ingredient')]
    public function removeIngredient(
        #[MapQueryParameter] int    $pizza_id,
        #[MapQueryParameter] string $ingredient_name,
        Request                     $request,
        EntityManagerInterface      $entityManager
    ): JsonResponse
    {
        $pizza = $entityManager->getRepository(Pizza::class)->find($pizza_id);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['name' => $ingredient_name]);

        if ($ingredient) {
            $pizzaIngredient = $entityManager->getRepository(PizzaIngredient::class)->findOneBy([
                'ingredient_id' => $ingredient->getId(),
                'pizza_id' => $pizza->getId()
            ]);

            if ($pizzaIngredient) {
                $pizza->removePizzaIngredient($pizzaIngredient);
                $entityManager->persist($pizzaIngredient);
                $entityManager->persist($pizza);
                $entityManager->flush();
            }
        }

        return $this->json($this->pizzaToArray($pizza));
    }
}
