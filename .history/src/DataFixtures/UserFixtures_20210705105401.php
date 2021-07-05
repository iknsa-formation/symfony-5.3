<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user->setEmail('user@gmail.com');

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'paris'
        ));


        $admin = new User();

        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);

        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'paris'
        ));
        $manager->persist($user);
        $manager->persist($admin);
        

        for ($i=0; $i < 30; $i++) { 
            $product = new Product();

            $product->setName($faker->name())
                    ->setPrice($faker->randomFloat())
                    ->setCreatedAt($faker->datetime())
                    ->setUpdatedAt($faker->datetime())
                    ->setStock($faker->random_int())
                    ->setCreatedBy($user)
            ;
            $manager->
        }

        $manager->flush();
    }


}
