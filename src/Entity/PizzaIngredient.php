<?php

namespace App\Entity;

use App\Repository\PizzaIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PizzaIngredientRepository::class)]
class PizzaIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pizzaIngredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pizza $pizza_id = null;

    #[ORM\ManyToOne(inversedBy: 'pizzaIngredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ingredient $ingredient_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPizzaId(): ?Pizza
    {
        return $this->pizza_id;
    }

    public function setPizzaId(?Pizza $pizza_id): static
    {
        $this->pizza_id = $pizza_id;

        return $this;
    }

    public function getIngredientId(): ?Ingredient
    {
        return $this->ingredient_id;
    }

    public function setIngredientId(?Ingredient $ingredient_id): static
    {
        $this->ingredient_id = $ingredient_id;

        return $this;
    }
}
