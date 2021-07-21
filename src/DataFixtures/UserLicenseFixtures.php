<?php

namespace App\DataFixtures;

use App\Entity\UserLicense;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserLicenseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $carLicenseMarie = new UserLicense();
        $carLicenseMarie->setLicense($this->getReference('car'));
        $carLicenseMarie->setUser($this->getReference('Marie'));
        $manager->persist($carLicenseMarie);

        $carLicenseJean = new UserLicense();
        $carLicenseJean->setLicense($this->getReference('car'));
        $carLicenseJean->setUser($this->getReference('Jean'));
        $manager->persist($carLicenseJean);

        $truckLicenseJean = new UserLicense();
        $truckLicenseJean->setLicense($this->getReference('truck'));
        $truckLicenseJean->setUser($this->getReference('Jean'));
        $manager->persist($truckLicenseJean);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            LicenseFixtures::class,
        ];
    }
}
