<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableEntity;
use App\Repository\SchoolClassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Blameable\Traits\BlameableEntity;

#[ORM\Entity(repositoryClass: SchoolClassRepository::class)]
#[UniqueConstraint(name: "code", columns: ["code"])]
class SchoolClass
{
    use BlameableEntity;
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'schoolClass', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\ManyToMany(targetEntity: Teacher::class, mappedBy: 'schoolClasses')]
    private Collection $teachers;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->teachers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $appointment->setSchoolClass($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getSchoolClass() === $this) {
                $appointment->setSchoolClass(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->code;
    }

    /**
     * @return Collection<int, Teacher>
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): static
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers->add($teacher);
            $teacher->addSchoolClass($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): static
    {
        if ($this->teachers->removeElement($teacher)) {
            $teacher->removeSchoolClass($this);
        }

        return $this;
    }
}
