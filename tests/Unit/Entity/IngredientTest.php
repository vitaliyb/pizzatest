<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Ingredient;
use PHPUnit\Framework\TestCase;

class IngredientTest extends TestCase
{


    public function testItWritesPriceAsWholeNumber()
    {
        $ingredient = new Ingredient();
        $ingredient->setPrice(2.50);

        $this->assertEquals(250, $ingredient->getPrice());

        $ingredient2 = new Ingredient();
        $ingredient2->setPrice(2);

        $this->assertEquals(200, $ingredient2->getPrice());
    }
}
