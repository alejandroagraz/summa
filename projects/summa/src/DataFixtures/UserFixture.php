<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private $faker;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create();
    }


    public function load(ObjectManager $em): void
    {
        $user = new User();
        $user->setUsername('joseagraz29@gmail.com');
        $user->setFirstname('Jose');
        $user->setLastname('Agraz');
        $user->setDni($this->faker->randomNumber(8, true));
        $user->setEmail('joseagraz29@gmail.com');
        $password = $this->hasher->hashPassword($user,'Passw*123');
        $user->setPassword($password);

        $em->persist($user);
        $em->flush();
    }
}