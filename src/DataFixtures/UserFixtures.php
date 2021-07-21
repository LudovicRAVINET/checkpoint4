<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use DateTime;

class UserFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $adminUser = new User();
        $regularUser = new User();

        $adminPassword = $this->encoder->encodePassword($adminUser, 'admin');
        $adminUser->setEmail('admin@costo.fr');
        $adminUser->setPassword($adminPassword);
        $adminUser->setFirstname('Administrator');
        $adminUser->setLastname('Administrator');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setBirthDate(new DateTime('1972-01-01'));

        $userPassword = $this->encoder->encodePassword($regularUser, 'password');
        $regularUser->setEmail('user@costo.fr');
        $regularUser->setPassword($userPassword);
        $regularUser->setFirstname('Jean');
        $regularUser->setLastname('Bon');
        $regularUser->setBirthDate(new DateTime('1963-07-22'));

        $manager->persist($adminUser);
        $manager->persist($regularUser);

        $manager->flush();
    }
}
