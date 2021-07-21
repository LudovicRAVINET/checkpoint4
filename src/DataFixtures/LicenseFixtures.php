<?php

namespace App\DataFixtures;

use App\Entity\License;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LicenseFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $carLicense = new License();
        $carLicense->setName('Permis voiture');
        $carLicense->setCategory('B');
        $this->addReference('car', $carLicense);
        $manager->persist($carLicense);

        $truckLicense = new License();
        $truckLicense->setName('Permis poids lourd');
        $truckLicense->setCategory('C');
        $this->addReference('truck', $truckLicense);
        $manager->persist($truckLicense);

        $manager->flush();
    }
}
