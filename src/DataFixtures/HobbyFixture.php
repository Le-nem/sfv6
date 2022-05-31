<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $job = array();
        for ($i = 0; $i < 50; $i++) {
            $job[$i] = new Hobby();
            $job[$i]->setDesignation($faker->country);
            $manager->persist($job[$i]);
        }

        $manager->flush();
    }
}
