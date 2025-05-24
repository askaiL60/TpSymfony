<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer des rôles
        $roleUser = new Role();
        $roleUser->setName('ROLE_USER');
        $manager->persist($roleUser);

        $roleAdmin = new Role();
        $roleAdmin->setName('ROLE_ADMIN');
        $manager->persist($roleAdmin);

        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setNom('Admin');
        $admin->setPrenom('Principal');
        $admin->setPassword($this->hasher->hashPassword($admin, 'password123'));
        $admin->addRole($roleUser);
        $admin->addRole($roleAdmin);
        $manager->persist($admin);

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setNom('Utilisateur');
        $user->setPrenom('Standard');
        $user->setPassword($this->hasher->hashPassword($user, 'password123'));
        $user->addRole($roleUser);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setNom('test');
        $user->setPrenom('Standard');
        $user->setPassword($this->hasher->hashPassword($user, 'password123'));
        $user->addRole($roleUser);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('utili@example.com');
        $user->setNom('uutile');
        $user->setPrenom('utile');
        $user->setPassword($this->hasher->hashPassword($user, 'password123'));
        $user->addRole($roleUser);
        $manager->persist($user);

        $manager->flush();
    }
}
