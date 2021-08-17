<?php

namespace App\Entity;

use App\Repository\MedicionGenericaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MedicionGenericaRepository::class)
 */
class MedicionGenerica
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $latitud;

    /**
     * @ORM\Column(type="float")
     */
    private $longitud;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $localizacion;

    /**
     * @ORM\Column(type="float")
     */
    private $altitud;

    /**
     * @ORM\Column(type="float")
     */
    private $bat;

    /**
     * @ORM\OneToMany(targetEntity=MedicionIndividual::class, mappedBy="ID_medicion")
     */
    private $medicionesIndividuales;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $autoria;

    public function __construct()
    {
        $this->medicionesIndividuales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getLatitud(): ?float
    {
        return $this->latitud;
    }

    public function setLatitud(float $latitud): self
    {
        $this->latitud = $latitud;

        return $this;
    }

    public function getLongitud(): ?float
    {
        return $this->longitud;
    }

    public function setLongitud(float $longitud): self
    {
        $this->longitud = $longitud;

        return $this;
    }

    public function getLocalizacion(): ?string
    {
        return $this->localizacion;
    }

    public function setLocalizacion(string $localizacion): self
    {
        $this->localizacion = $localizacion;

        return $this;
    }

    public function getAltitud(): ?float
    {
        return $this->altitud;
    }

    public function setAltitud(float $altitud): self
    {
        $this->altitud = $altitud;

        return $this;
    }

    /**
     * @return Collection|MedicionIndividual[]
     */
    public function getMedicionesIndividuales(): Collection
    {
        return $this->medicionesIndividuales;
    }

    public function addMedicionesIndividuale(MedicionIndividual $medicionesIndividuale): self
    {
        if (!$this->medicionesIndividuales->contains($medicionesIndividuale)) {
            $this->medicionesIndividuales[] = $medicionesIndividuale;
            $medicionesIndividuale->setIDMedicion($this);
        }

        return $this;
    }

    public function removeMedicionesIndividuale(MedicionIndividual $medicionesIndividuale): self
    {
        if ($this->medicionesIndividuales->removeElement($medicionesIndividuale)) {
            // set the owning side to null (unless already changed)
            if ($medicionesIndividuale->getIDMedicion() === $this) {
                $medicionesIndividuale->setIDMedicion(null);
            }
        }

        return $this;
    }

    public function getAltura(): ?float
    {
        return $this->altura;
    }

    public function setAltura(float $altura): self
    {
        $this->altura = $altura;

        return $this;
    }


    public function getBat(): ?float
    {
        return $this->bat;
    }

    public function setBat(float $bat): self
    {
        $this->bat = $bat;

        return $this;
    }

}
