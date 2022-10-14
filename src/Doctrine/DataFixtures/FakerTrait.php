<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use Faker\Factory;
use Faker\Generator;

trait FakerTrait
{
    private ?Generator $faker = null;

    public function faker(): Generator
    {
        if (null === $this->faker) {
            $this->faker = Factory::create('fr_FR');
        }

        return $this->faker;
    }
}
