<?php

namespace App\Service;


use App\Entity\Ingredient;
use App\Entity\Pizza;
use App\Entity\PizzaIngredient;
use Doctrine\ORM\EntityManagerInterface;

class ApiService
{
    private EntityManagerInterface $entityManager;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function listPizzas()
    {
        $pizzas = $this->entityManager->getRepository(Pizza::class)->findAll();

        return array_map(function (Pizza $pizza) {
            return $this->pizzaToArray($pizza);
        }, $pizzas);
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

    public function removeIngredient($pizzaId, $ingredientName)
    {
        $pizza = $this->entityManager->getRepository(Pizza::class)->find($pizzaId);
        $ingredient = $this->entityManager->getRepository(Ingredient::class)->findOneBy(['name' => $ingredientName]);

        if ($ingredient) {
            $pizzaIngredient = $this->entityManager->getRepository(PizzaIngredient::class)->findOneBy([
                'ingredient_id' => $ingredient->getId(),
                'pizza_id' => $pizza->getId()
            ]);

            if ($pizzaIngredient) {
                $pizza->removePizzaIngredient($pizzaIngredient);
                $this->entityManager->persist($pizzaIngredient);
                $this->entityManager->persist($pizza);
                $this->entityManager->flush();
            }
        }

        return $this->pizzaToArray($pizza);
    }

    public function addPizza($name, $ingredients)
    {
        $pizza = new Pizza();
        $pizza->setName($name);

        foreach ($ingredients as $ingredientProperties) {
            $this->addIngredientToPizza($pizza, $ingredientProperties['name'], $ingredientProperties['price']);
        }

        $this->entityManager->persist($pizza);

        return $this->pizzaToArray($pizza);
    }

    private function addIngredientToPizza(Pizza $pizza, $ingredientName, $ingredientPrice): void
    {
        $ingredient = $this->entityManager->getRepository(Ingredient::class)->findOneBy(['name' => $ingredientName]);

        if (!$ingredient) {
            $ingredient = new Ingredient();
            $ingredient->setName($ingredientName);
            $ingredient->setPrice($ingredientPrice);

            $this->entityManager->persist($ingredient);
        }

        $pizza->addIngredient($ingredient);
    }

    public function addIngredient($pizza_id, $ingredient_name, $ingredient_price)
    {
        $pizza = $this->entityManager->getRepository(Pizza::class)->find($pizza_id);

        $this->addIngredientToPizza($pizza, $ingredient_name, $ingredient_price);

        $this->entityManager->persist($pizza);

        return $this->pizzaToArray($pizza);
    }
}
