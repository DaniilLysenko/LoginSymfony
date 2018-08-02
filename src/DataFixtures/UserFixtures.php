<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    private $container;

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('daniil');
        $user->setPassword(password_hash('12345', PASSWORD_DEFAULT));
        $manager->persist($user);
        $manager->flush();
    }
}