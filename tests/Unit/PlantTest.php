<?php

namespace Tests\Unit;

use App\Models\Plant;
use PHPUnit\Framework\TestCase;

class PlantTest extends TestCase
{
    /**
     * Test that the get common name methond return string
     */
    public function testThatGetCommonNameMethodReturnsAString(): void
    {
        $plant = new Plant();

        $plant->setCommonName('NAME');

        $this->assertEquals('NAME', $plant->getCommonName());
    }
}
