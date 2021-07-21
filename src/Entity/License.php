<?php

namespace App\Entity;

use App\Repository\LicenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LicenseRepository::class)
 */
class License
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=UserLicense::class, mappedBy="license", orphanRemoval=true)
     */
    private $userLicenses;

    /**
     * @ORM\OneToMany(targetEntity=VehicleType::class, mappedBy="license", orphanRemoval=true)
     */
    private $vehicleTypes;

    public function __construct()
    {
        $this->userLicenses = new ArrayCollection();
        $this->vehicleTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|UserLicense[]
     */
    public function getUserLicenses(): Collection
    {
        return $this->userLicenses;
    }

    public function addUserLicense(UserLicense $userLicense): self
    {
        if (!$this->userLicenses->contains($userLicense)) {
            $this->userLicenses[] = $userLicense;
            $userLicense->setLicense($this);
        }

        return $this;
    }

    public function removeUserLicense(UserLicense $userLicense): self
    {
        if ($this->userLicenses->removeElement($userLicense)) {
            // set the owning side to null (unless already changed)
            if ($userLicense->getLicense() === $this) {
                $userLicense->setLicense(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VehicleType[]
     */
    public function getVehicleTypes(): Collection
    {
        return $this->vehicleTypes;
    }

    public function addVehicleType(VehicleType $vehicleType): self
    {
        if (!$this->vehicleTypes->contains($vehicleType)) {
            $this->vehicleTypes[] = $vehicleType;
            $vehicleType->setLicense($this);
        }

        return $this;
    }

    public function removeVehicleType(VehicleType $vehicleType): self
    {
        if ($this->vehicleTypes->removeElement($vehicleType)) {
            // set the owning side to null (unless already changed)
            if ($vehicleType->getLicense() === $this) {
                $vehicleType->setLicense(null);
            }
        }

        return $this;
    }
}
