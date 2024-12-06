<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[Vich\Uploadable]
#[ApiResource(

        operations: [

            new Get(normalizationContext: ['groups' => 'conference:item']),

            new GetCollection(normalizationContext: ['groups' => 'conference:list'])

        ],

    order: ['year' => 'DESC', 'city' => 'ASC'],

    paginationEnabled: false,

)]
class Picture
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['picture:list', 'picture:item'])]
    private ?int $id = null;
    #[Vich\UploadableField(mapping: 'pictures', fileNameProperty: 'path')]
    private ?string $file = null;

    #[ORM\Column(length: 255)]
    #[Groups(['picture:list', 'picture:item'])]
    private ?string $path = null;

    #[ORM\Column(type: "datetime_immutable")]
    #[Groups(['picture:list', 'picture:item'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Advert::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "cascade")]
    #[Groups(['picture:list', 'picture:item'])]
    private ?Advert $advert = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?File $file): static
    {
        $this->file = $file;

        if ($file) {
            $this->createdAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): static
    {
        $this->advert = $advert;

        return $this;
    }
}
