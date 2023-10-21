<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableEntity;
use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]

#[UniqueConstraint(name: "code", columns: ["code"])]
class Teacher
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 32, unique: true)]
    private ?string $token = null;

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $appointments;

    #[ORM\ManyToMany(targetEntity: SchoolClass::class, inversedBy: 'teachers')]
    private Collection $schoolClasses;

    #[ORM\ManyToMany(targetEntity: TimeFrame::class, inversedBy: 'availableTeachers')]
    private Collection $availableTimeFrames;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->schoolClasses = new ArrayCollection();
        $this->availableTimeFrames = new ArrayCollection();
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setTeacher($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getTeacher() === $this) {
                $appointment->setTeacher(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->lastName. ', ' .$this->getFirstName() .' (Kürzel ' .$this->getCode() .')';
    }

    /**
     * @return Collection<int, SchoolClass>
     */
    public function getSchoolClasses(): Collection
    {
        return $this->schoolClasses;
    }

    public function addSchoolClass(SchoolClass $schoolClass): static
    {
        if (!$this->schoolClasses->contains($schoolClass)) {
            $this->schoolClasses->add($schoolClass);
        }

        return $this;
    }

    public function removeSchoolClass(SchoolClass $schoolClass): static
    {
        $this->schoolClasses->removeElement($schoolClass);

        return $this;
    }

    public function getOccupiedTimeFrameIds() :array
    {
        $return = [];
        foreach ($this->getAppointments() as $appointment) {
            $return[] = $appointment->getTimeFrame()->getId();
        }
        return $return;
    }

    /**
     * @return Collection<int, TimeFrame>
     */
    public function getAvailableTimeFrames(): Collection
    {
        return $this->availableTimeFrames;
    }

    public function addAvailableTimeFrame(TimeFrame $availableTimeFrame): static
    {
        if (!$this->availableTimeFrames->contains($availableTimeFrame)) {
            $this->availableTimeFrames->add($availableTimeFrame);
        }

        return $this;
    }

    public function removeAvailableTimeFrame(TimeFrame $availableTimeFrame): static
    {
        $this->availableTimeFrames->removeElement($availableTimeFrame);

        return $this;
    }
}
