<?php

namespace App\Entity;

use App\Repository\ProveedorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProveedorRepository::class)]
class Proveedor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,  nullable: false)]
    private string $nombre;

    #[ORM\OneToMany(targetEntity: Actividad::class, mappedBy: 'proveedor')]
    private Collection $actividad;

    public function __construct()
    {
        $this->actividad = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection<int, Actividad>
     */
    public function getActividad(): Collection
    {
        return $this->actividad;
    }

    public function addActividad(Actividad $actividad): static
    {
        if (!$this->actividad->contains($actividad)) {
            $this->actividad->add($actividad);
            $actividad->setProveedor($this);
        }

        return $this;
    }

    public function removeActividad(Actividad $actividad): static
    {
        if ($this->actividad->removeElement($actividad)) {
            
            if ($actividad->getProveedor() === $this) {
                $actividad->setProveedor(null);
            }
        }

        return $this;
    }
}
