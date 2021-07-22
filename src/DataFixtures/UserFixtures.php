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
        $carUser = new User();

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
        $this->addReference('Jean', $regularUser);

        $userCarPassword = $this->encoder->encodePassword($carUser, 'password');
        $carUser->setEmail('marie@costo.fr');
        $carUser->setPassword($userCarPassword);
        $carUser->setFirstname('Marie');
        $carUser->setLastname('Rigaud');
        $carUser->setBirthDate(new DateTime('1983-09-02'));
        $this->addReference('Marie', $carUser);

        $manager->persist($adminUser);
        $manager->persist($regularUser);
        $manager->persist($carUser);

        $manager->flush();
    }
}
