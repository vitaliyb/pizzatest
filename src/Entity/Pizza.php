<?php

namespace App\Entity;

use App\Repository\PizzaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PizzaRepository::class)]
class Pizza
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\OneToMany(mappedBy: 'pizza_id', targetEntity: PizzaIngredient::class, orphanRemoval: true)]
    private Collection $pizzaIngredients;

    public function __construct()
    {
        $this->pizzaIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, PizzaIngredient>
     */
    public function getPizzaIngredients(): Collection
    {
        return $this->pizzaIngredients;
    }

    public function addPizzaIngredient(PizzaIngredient $pizzaIngredient): static
    {
        if (!$this->pizzaIngredients->contains($pizzaIngredient)) {
            $this->pizzaIngredients->add($pizzaIngredient);
            $pizzaIngredient->setPizzaId($this);
        }

        return $this;
    }

    public function removePizzaIngredient(PizzaIngredient $pizzaIngredient): static
    {
        if ($this->pizzaIngredients->removeElement($pizzaIngredient)) {
            // set the owning side to null (unless already changed)
            if ($pizzaIngredient->getPizzaId() === $this) {
                $pizzaIngredient->setPizzaId(null);
            }
        }

        return $this;
    }
}
