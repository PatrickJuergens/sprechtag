<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableEntity;
use App\Repository\TimeFrameRepository;
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

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

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
}
