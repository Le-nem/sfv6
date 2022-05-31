<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $job = array();
        for ($i = 0; $i < 10; $i++) {
            $job[$i] = new Job();
            $job[$i]->setDesignation($faker->company);
            $manager->persist($job[$i]);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
