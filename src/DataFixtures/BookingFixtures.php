<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DateTime;

class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $booking1 = new Booking();
        $booking1->setStartDate(new DateTime('2021-07-24 08:00:00'));
        $booking1->setReturnDate(new DateTime('2021-07-26 18:00:00'));
        $booking1->setVehicle($this->getReference('jumpy1'));
        $booking1->setUser($this->getReference('Marie'));
        $manager->persist($booking1);

        $booking2 = new Booking();
        $booking2->setStartDate(new DateTime('2021-07-24 08:00:00'));
        $booking2->setReturnDate(new DateTime('2021-07-25 18:00:00'));
        $booking2->setVehicle($this->getReference('ducato1'));
        $booking2->setUser($this->getReference('Jean'));
        $manager->persist($booking2);

        $booking3 = new Booking();
        $booking3->setStartDate(new DateTime('2021-07-26 08:00:00'));
        $booking3->setReturnDate(new DateTime('2021-07-31 18:00:00'));
        $booking3->setVehicle($this->getReference('truck1'));
        $booking3->setUser($this->getReference('Jean'));
        $manager->persist($booking3);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            VehicleFixtures::class,
        ];
    }
}
