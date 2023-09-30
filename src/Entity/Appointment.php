<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[UniqueConstraint(name: "teacher_timeFrame", columns: ["teacher_id", "time_frame_id"])]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $visitorFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $visitorLastName = null;

    #[ORM\Column(length: 255)]
    private ?string $studentFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $studentLastName = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TimeFrame $timeFrame = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    private ?SchoolClass $schoolClass = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 32)]
    private ?string $token = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisitorFirstName(): ?string
    {
        return $this->visitorFirstName;
    }

    public function setVisitorFirstName(string $visitorFirstName): static
    {
        $this->visitorFirstName = $visitorFirstName;

        return $this;
    }

    public function getVisitorLastName(): ?string
    {
        return $this->visitorLastName;
    }

    public function setVisitorLastName(string $visitorLastName): static
    {
        $this->visitorLastName = $visitorLastName;

        return $this;
    }

    public function getStudentFirstName(): ?string
    {
        return $this->studentFirstName;
    }

    public function setStudentFirstName(string $studentFirstName): static
    {
        $this->studentFirstName = $studentFirstName;

        return $this;
    }

    public function getStudentLastName(): ?string
    {
        return $this->studentLastName;
    }

    public function setStudentLastName(string $studentLastName): static
    {
        $this->studentLastName = $studentLastName;

        return $this;
    }

    public function getTimeFrame(): ?TimeFrame
    {
        return $this->timeFrame;
    }

    public function setTimeFrame(?TimeFrame $timeFrame): static
    {
        $this->timeFrame = $timeFrame;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getSchoolClass(): ?SchoolClass
    {
        return $this->schoolClass;
    }

    public function setSchoolClass(?SchoolClass $schoolClass): static
    {
        $this->schoolClass = $schoolClass;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
