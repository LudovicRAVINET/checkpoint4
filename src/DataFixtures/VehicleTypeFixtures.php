<?php

namespace App\DataFixtures;

use App\Entity\VehicleType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VehicleTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $jumpy = new VehicleType();
        $jumpy->setName('Jumpy');
        $jumpy->setVolume(6);
        $jumpy->setPicture('jumpy.png');
        $jumpy->setLicense($this->getReference('car'));
        $this->addReference('jumpy', $jumpy);
        $manager->persist($jumpy);

        $iveco = new VehicleType();
        $iveco->setName('Iveco');
        $iveco->setVolume(11);
        $iveco->setPicture('iveco.png');
        $iveco->setLicense($this->getReference('car'));
        $this->addReference('iveco', $iveco);
        $manager->persist($iveco);

        $ducato = new VehicleType();
        $ducato->setName('Ducato');
        $ducato->setVolume(20);
        $ducato->setPicture('ducato.png');
        $ducato->setLicense($this->getReference('car'));
        $this->addReference('ducato', $ducato);
        $manager->persist($ducato);

        $truck = new VehicleType();
        $truck->setName('Truck');
        $truck->setVolume(100);
        $truck->setPicture('truck.jpg');
        $truck->setLicense($this->getReference('truck'));
        $this->addReference('truckType', $truck);
        $manager->persist($truck);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LicenseFixtures::class,
        ];
    }
}
