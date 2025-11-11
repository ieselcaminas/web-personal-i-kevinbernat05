<?php

namespace App\Entity;

use App\Repository\LibroRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibroRepository::class)]
class Libro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(length: 255)]
    private ?string $Genero = null;

    // clave ajena (relación muchos a uno)
    #[ORM\ManyToOne(targetEntity: Autor::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Autor $autor;

    public function getAutor(): ?Autor
    {
        return $this->autor;
    }

    public function setAutor(?Autor $autor): static
    {
        $this->autor = $autor;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function gettitulo(): ?string
    {
        return $this->titulo;
    }

    public function settitulo(string $titulo): static
    {
        $this->titulo = $titulo;

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
