<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'krsq_posts')]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $postTitle = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Favorie::class)]
    private Collection $lien;

    public function __construct()
    {
        $this->lien = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostTitle(): ?string
    {
        return $this->postTitle;
    }

    public function setPostTitle(string $postTitle): self
    {
        $this->postTitle = $postTitle;

        return $this;
    }

    /**
     * @return Collection<int, Favorie>
     */
    public function getLien(): Collection
    {
        return $this->lien;
    }

    public function addLien(Favorie $lien): self
    {
        if (!$this->lien->contains($lien)) {
            $this->lien->add($lien);
            $lien->setPost($this);
        }

        return $this;
    }

    public function removeLien(Favorie $lien): self
    {
        if ($this->lien->removeElement($lien)) {
            // set the owning side to null (unless already changed)
            if ($lien->getPost() === $this) {
                $lien->setPost(null);
            }
        }

        return $this;
    }
}
