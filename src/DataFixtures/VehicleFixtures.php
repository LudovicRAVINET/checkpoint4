<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VehicleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $jumpy1 = new Vehicle();
        $jumpy1->setRegistration('FD-543-PE');
        $jumpy1->setType($this->getReference('jumpy'));
        $this->addReference('jumpy1', $jumpy1);
        $manager->persist($jumpy1);

        $jumpy2 = new Vehicle();
        $jumpy2->setRegistration('LS-731-SM');
        $jumpy2->setType($this->getReference('jumpy'));
        $this->addReference('jumpy2', $jumpy2);
        $manager->persist($jumpy2);

        $iveco1 = new Vehicle();
        $iveco1->setRegistration('ME-571-SH');
        $iveco1->setType($this->getReference('iveco'));
        $this->addReference('iveco1', $iveco1);
        $manager->persist($iveco1);

        $iveco2 = new Vehicle();
        $iveco2->setRegistration('FE-588-ZA');
        $iveco2->setType($this->getReference('iveco'));
        $this->addReference('iveco2', $iveco2);
        $manager->persist($iveco2);

        $ducato1 = new Vehicle();
        $ducato1->setRegistration('TZ-661-CV');
        $ducato1->setType($this->getReference('ducato'));
        $this->addReference('ducato1', $ducato1);
        $manager->persist($ducato1);

        $truck1 = new Vehicle();
        $truck1->setRegistration('DZ-963-XV');
        $truck1->setType($this->getReference('truckType'));
        $this->addReference('truck1', $truck1);
        $manager->persist($truck1);

        $truck2 = new Vehicle();
        $truck2->setRegistration('LK-564-PO');
        $truck2->setType($this->getReference('truckType'));
        $this->addReference('truck2', $truck2);
        $manager->persist($truck2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            VehicleTypeFixtures::class,
        ];
    }
}
