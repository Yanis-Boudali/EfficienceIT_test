<?php

namespace App\DataFixtures;

use App\Entity\Departement;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class DepartementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $nbDepartements = 5;
        $name = ['Direction','RH','Informatique','Commercial','BE'];
        $email = ['Direction@gmail.com', 'rh@gmail.com','commercial@gmail.com','info@gmail.com','be@gmail.com'];
        for ($i=0; $i < $nbDepartements; $i++){
            $departement = new Departement();
            $departement->setName($name[$i]);
            $departement->setEmail($email[$i]);
            $manager->persist($departement);
        }
        $manager->flush();
    }
}
