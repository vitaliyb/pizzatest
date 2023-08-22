<?php

namespace App\Factory;

use App\Entity\PizzaIngredient;
use App\Repository\PizzaIngredientRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<PizzaIngredient>
 *
 * @method        PizzaIngredient|Proxy                     create(array|callable $attributes = [])
 * @method static PizzaIngredient|Proxy                     createOne(array $attributes = [])
 * @method static PizzaIngredient|Proxy                     find(object|array|mixed $criteria)
 * @method static PizzaIngredient|Proxy                     findOrCreate(array $attributes)
 * @method static PizzaIngredient|Proxy                     first(string $sortedField = 'id')
 * @method static PizzaIngredient|Proxy                     last(string $sortedField = 'id')
 * @method static PizzaIngredient|Proxy                     random(array $attributes = [])
 * @method static PizzaIngredient|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PizzaIngredientRepository|RepositoryProxy repository()
 * @method static PizzaIngredient[]|Proxy[]                 all()
 * @method static PizzaIngredient[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static PizzaIngredient[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static PizzaIngredient[]|Proxy[]                 findBy(array $attributes)
 * @method static PizzaIngredient[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static PizzaIngredient[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PizzaIngredientFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'ingredient_id' => IngredientFactory::new(),
            'pizza_id' => PizzaFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(PizzaIngredient $pizzaIngredient): void {})
        ;
    }

    protected static function getClass(): string
    {
        return PizzaIngredient::class;
    }
}
