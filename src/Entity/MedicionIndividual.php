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
    private $nombre;

    /**
     * @ORM\Column(type="float")
     */
    private $mediaMagnitud;

    /**
     * @ORM\Column(type="float")
     */
    private $temp_infrarroja;

    /**
     * @ORM\Column(type="float")
     */
    private $temp_sensor;

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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

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

    public function getGrafico(): ?string
    {
        return $this->grafico;
    }

    public function setGrafico(string $grafico): self {
        $this->grafico = $grafico;

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
}
