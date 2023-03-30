<?php

namespace App\Entity;

use App\Repository\WpTermsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WpTermsRepository::class)]
#[ORM\Table(name: 'krsq_terms')]
class WpTerms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'term_id')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $termGroup = null;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTermGroup(): ?int
    {
        return $this->termGroup;
    }

    public function setTermGroup(int $termGroup): self
    {
        $this->termGroup = $termGroup;

        return $this;
    }
}
