<?php

namespace App\Service;

use App\Entity\Vehicle;
use App\Entity\VehicleType;
use App\Repository\BookingRepository;
use App\Repository\VehicleRepository;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

class VehicleAvailability
{
    private BookingRepository $bookingRepository;
    private VehicleRepository $vehicleRepository;

    public function __construct(BookingRepository $bookingRepository, VehicleRepository $vehicleRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function isAvailable(Vehicle $vehicle, DateTime $departureDay, DateTime $arrivalDay): bool
    {
        $available = $this->bookingRepository->findOneByAvailable($vehicle, $departureDay, $arrivalDay);

        if ($available == null)
            return true;

        return false;
    }

    public function oneIsAvailable(VehicleType $vehicleType, DateTime $departureDay, DateTime $arrivalDay): ?Vehicle
    {
        $vehicles = $this->vehicleRepository->findBy(['type' => $vehicleType]);

        foreach ($vehicles as $vehicle) {
            if ($this->isAvailable($vehicle, $departureDay, $arrivalDay)) {
                return $vehicle;
            }
        }

        return null;
    }
}
