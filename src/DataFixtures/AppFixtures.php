<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        /**
         *  Esto es como el DatabaseSeeder de Laravel, pero con la
         *  sintáxis de Symfony 6
         */

        // $alumno = new Alumnos();
        // $manager->persist($alumno);

        $manager->flush();
    }
}
