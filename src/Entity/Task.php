<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var DateTime A "Y-m-d H:i:s" formatted value
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\NotBlank(message: "Vérifier dans votre contrôleur qu'une date de création est bien renseigné")]
    #[Assert\DateTime]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 180, nullable: false)]
    #[Assert\NotBlank(message: "Un titre est obligatoire")]
    #[Assert\Length(min: 1, max: 180)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank(message: "Une information sur la tâche a faire est obligatoire")]
    private ?string $content = null;

    #[ORM\Column(nullable: false)]
    private bool $isDone = false;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->isDone = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): static
    {
        if (!is_bool($isDone)) {
            throw new \InvalidArgumentException("La valeur de 'isDone' doit être un booléen.");
        }

        $this->isDone = $isDone;
        return $this;
    }

    //enlèvement de la function toggle($flag) car redondant avec la function setDone($isDone) 

}
