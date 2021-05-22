<?php

namespace App\Entity;

use App\Repository\MedicionIndividualRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MedicionIndividualRepository::class)
 */
class MedicionIndividual
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MedicionGenerica::class, inversedBy="medicionesIndividuales")
     * @ORM\JoinColumn(nullable=false)
     */
    private $generica;

    /**
     * @ORM\Column(type="float")
     */
    private $declinacion;

    /**
     * @ORM\Column(type="float")
     */
    private $azimut;

    /**
     * @ORM\Column(type="float")
     */
    private $magnitud;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDeclinacion(): ?float
    {
        return $this->declinacion;
    }

    public function setDeclinacion(float $declinacion): self
    {
        $this->declinacion = $declinacion;

        return $this;
    }

    public function getAzimut(): ?float
    {
        return $this->azimut;
    }

    public function setAzimut(float $azimut): self
    {
        $this->azimut = $azimut;

        return $this;
    }

    public function getMagnitud(): ?float
    {
        return $this->magnitud;
    }

    public function setMagnitud(float $magnitud): self
    {
        $this->magnitud = $magnitud;

        return $this;
    }

    public function getGenerica(): ?MedicionGenerica
    {
        return $this->generica;
    }

    public function setGenerica(?MedicionGenerica $generica): self
    {
        $this->generica = $generica;

        return $this;
    }
}
