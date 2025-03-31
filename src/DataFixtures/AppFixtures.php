<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
//         $adminUser = new User();
//         $adminUser
//             ->setEmail('admin@example.com')
//             // hashed but since this is example app password is just 'pass'
//             ->setPassword('$2y$13$QhdS6dosllvCl9Tns8/NOuS1rf7J3RBU1Fofv7N/MvnS9M/BsVntm')
//             ->setRoles(['ROLE_ADMIN'])
//         ;
//         $manager->persist($adminUser);

//        $manager->flush();
    }
}
