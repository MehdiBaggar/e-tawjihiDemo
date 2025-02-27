<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use App\Traits\timeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use http\Message;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\Translation\t;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Personne
{
    use timeStampTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'veuiller saisir votre prénom !')]
    #[Assert\Length(min: 3,minMessage: 'Veiller avoir au moins 3 lettre dans vote prénom')]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'veuiller saisir votre nom !')]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank(message: 'veuiller saisir votre age !')]
    private ?int $age = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Profile $profile = null;

    /**
     * @var Collection<int, Hobby>
     */
    #[ORM\ManyToMany(targetEntity: Hobby::class)]
    private Collection $hobbies;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    private ?Job $job = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, UserMetaData>
     */
    #[ORM\OneToMany(targetEntity: UserMetaData::class, mappedBy: 'personne')]
    private Collection $userMetaData;

    public function __construct()
    {
        $this->hobbies = new ArrayCollection();
        $this->userMetaData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection<int, Hobby>
     */
    public function getHobbies(): Collection
    {
        return $this->hobbies;
    }

    public function addHobby(Hobby $hobby): static
    {
        if (!$this->hobbies->contains($hobby)) {
            $this->hobbies->add($hobby);
        }

        return $this;
    }

    public function removeHobby(Hobby $hobby): static
    {
        $this->hobbies->removeElement($hobby);

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist()]
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }
    #[ORM\PreUpdate()]
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, UserMetaData>
     */
    public function getUserMetaData(): Collection
    {
        return $this->userMetaData;
    }

    public function addUserMetaData(UserMetaData $userMetaData): static
    {
        if (!$this->userMetaData->contains($userMetaData)) {
            $this->userMetaData->add($userMetaData);
            $userMetaData->setPersonne($this);
        }

        return $this;
    }

    public function removeUserMetaData(UserMetaData $userMetaData): static
    {
        if ($this->userMetaData->removeElement($userMetaData)) {
            // set the owning side to null (unless already changed)
            if ($userMetaData->getPersonne() === $this) {
                $userMetaData->setPersonne(null);
            }
        }

        return $this;
    }
}
