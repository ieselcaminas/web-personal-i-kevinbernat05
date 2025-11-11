<?php

namespace App\Entity;

use App\Repository\AutorRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Libro;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: AutorRepository::class)]
class Autor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $fechaNacimiento = null;

    #[ORM\Column(length: 255)]
    private ?string $Genero = null;

    // relación uno a muchos
    #[ORM\OneToMany(mappedBy: 'autor', targetEntity: Libro::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $libros;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getnombre(): ?string
    {
        return $this->nombre;
    }

    public function setnombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(\DateTimeInterface $fechaNacimiento): static
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    public function getGenero(): ?string
    {
        return $this->Genero;
    }

    public function setGenero(string $Genero): static
    {
        $this->Genero = $Genero;

        return $this;
    }
}
