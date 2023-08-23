<?php

namespace App\Controller;

use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function listPizzas(ApiService $service): JsonResponse
    {
        return $this->json($service->listPizzas());
    }

    #[Route('/api/add_pizza', name: 'api.add_pizza')]
    public function addPizza(
        #[MapQueryParameter] string $name,
        #[MapQueryParameter] array  $ingredients,
        ApiService                  $service
    ): JsonResponse
    {
        return $this->json($service->addPizza($name, $ingredients));
    }

    #[Route('/api/add_ingredient', name: 'api.add_ingredient')]
    public function addIngredient(
        #[MapQueryParameter] int    $pizza_id,
        #[MapQueryParameter] string $ingredient_name,
        #[MapQueryParameter] float  $ingredient_price,
        ApiService                  $service
    ): JsonResponse
    {
        return $this->json($service->addIngredient($pizza_id, $ingredient_name, $ingredient_price));
    }

    #[Route('/api/remove_ingredient', name: 'api.remove_ingredient')]
    public function removeIngredient(
        #[MapQueryParameter] int    $pizza_id,
        #[MapQueryParameter] string $ingredient_name,
        ApiService                  $service,
    ): JsonResponse
    {
        return $this->json($service->removeIngredient($pizza_id, $ingredient_name));
    }
}
