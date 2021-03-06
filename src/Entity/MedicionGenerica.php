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
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="time")
     */
    private $hora;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $archivo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $grafico;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=6)
     */
    private $latitud;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=6)
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
     * @ORM\Column(type="float")
     */
    private $temp_infrarroja;

    /**
     * @ORM\Column(type="float")
     */
    private $temp_sensor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $autoria;

    private $observaciones;

    public function __construct()
    {
        $this->medicionesIndividuales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(\DateTimeInterface $hora): self
    {
        $this->hora = $hora;

        return $this;
    }

    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    public function setArchivo(string $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }

    public function getGrafico(): ?string
    {
        return $this->grafico;
    }

    public function setGrafico(string $grafico): self {
        $this->grafico = $grafico;

        return $this;
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

    public function getAutoria(): ?string
    {
        return $this->autoria;
    }

    public function setAutoria(string $autor): self {
        $this->autoria = $autor;
        
        return $this;
    }

    public function getTempInfrarroja(): ?float
    {
        return $this->temp_infrarroja;
    }

    public function setTempInfrarroja(float $temp_infrarroja): self
    {
        $this->temp_infrarroja = $temp_infrarroja;

        return $this;
    }

    public function getTempSensor(): ?float
    {
        return $this->temp_sensor;
    }

    public function setTempSensor(float $temp_sensor): self
    {
        $this->temp_sensor = $temp_sensor;

        return $this;
    }

    public function getObservaciones(): ?string 
    {
        return $this->observaciones;
    }

    public function setObservaciones(string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

}
