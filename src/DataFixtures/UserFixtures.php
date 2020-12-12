<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'pass'));
        $manager->persist($user);
        $this->addReference('user-test', $user);

        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'pass'));
        $manager->persist($admin);
        $this->addReference('user-admin', $admin);

        $manager->flush();
    }
}
