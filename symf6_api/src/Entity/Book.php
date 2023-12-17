<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Title cannot be blank.')]
    #[Assert\Length(max: 50, maxMessage: 'Title cannot be longer than {{ limit }} characters.')]
    private ?string $title = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Author cannot be blank.')]
    #[Assert\Length(max: 50, maxMessage: 'Author cannot be longer than {{ limit }} characters.')]
        private ?string $author = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'Description cannot be longer than {{ limit }} characters.')]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: ['float', 'null'], message: 'Price must be a valid number or null.')]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
