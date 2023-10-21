<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableEntity;
use App\Repository\TimeFrameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Blameable\Traits\BlameableEntity;

#[ORM\Entity(repositoryClass: TimeFrameRepository::class)]
#[UniqueConstraint(name: "name", columns: ["name"])]
class TimeFrame
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Teacher::class, mappedBy: 'availableTimeFrames')]
    private Collection $availableTeachers;

    public function __construct()
    {
        $this->availableTeachers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return Collection<int, Teacher>
     */
    public function getAvailableTeachers(): Collection
    {
        return $this->availableTeachers;
    }

    public function addAvailableTeacher(Teacher $availableTeacher): static
    {
        if (!$this->availableTeachers->contains($availableTeacher)) {
            $this->availableTeachers->add($availableTeacher);
            $availableTeacher->addAvailableTimeFrame($this);
        }

        return $this;
    }

    public function removeAvailableTeacher(Teacher $availableTeacher): static
    {
        if ($this->availableTeachers->removeElement($availableTeacher)) {
            $availableTeacher->removeAvailableTimeFrame($this);
        }

        return $this;
    }
}
