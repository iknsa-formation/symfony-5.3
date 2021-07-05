<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
        $manager->flush();
    }
}
