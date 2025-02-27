<?php

namespace App\Entity;

use App\Repository\UserMetaDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserMetaDataRepository::class)]
class UserMetaData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fieldName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fieldType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fieldValue = null;

    #[ORM\ManyToOne(inversedBy: 'userMetaData')]
    private ?Personne $personne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function setFieldName(?string $fieldName): static
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function getFieldType(): ?string
    {
        return $this->fieldType;
    }

    public function setFieldType(?string $fieldType): static
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    public function getFieldValue(): ?string
    {
        return $this->fieldValue;
    }

    public function setFieldValue(?string $fieldValue): static
    {
        $this->fieldValue = $fieldValue;

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): static
    {
        $this->personne = $personne;

        return $this;
    }
}
