<?php

namespace App\Controller;

use App\Entity\Pizza;
use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function listPizzas(PizzaRepository $pizzaRepository)
    {
        return $this->json(
            array_map(function (Pizza $pizza) {
                return [
                    'name' => $pizza->getName(),
                    'price' => $pizza->getPrice()
                ];
            }, $pizzaRepository->findAll())
        );
    }

    #[Route('/api/add_pizza', name: 'api.add_pizza')]
    public function addPizza()
    {
        // TODO: write
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
