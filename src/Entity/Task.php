<?php

namespace App\Entity;

use App\DTO\TaskDTO;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subTasks')]
    private $parentTask;

    #[ORM\OneToMany(mappedBy: 'parentTask', targetEntity: self::class)]
    private $subTasks;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $completedAt;

    public function __construct()
    {
        $this->subTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParentTask(): ?self
    {
        return $this->parentTask;
    }

    public function setParentTask(?self $parentTask): self
    {
        $this->parentTask = $parentTask;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSubTasks(): Collection
    {
        return $this->subTasks;
    }

    public function addSubTask(self $subTask): self
    {
        if (!$this->subTasks->contains($subTask)) {
            $this->subTasks[] = $subTask;
            $subTask->setParentTask($this);
        }

        return $this;
    }

    public function removeSubTask(self $subTask): self
    {
        if ($this->subTasks->removeElement($subTask)) {
            // set the owning side to null (unless already changed)
            if ($subTask->getParentTask() === $this) {
                $subTask->setParentTask(null);
            }
        }

        return $this;
    }

    public function toDTO(): TaskDTO 
    {
        return new TaskDTO(
            $this->getId(),
            $this->getName(),
            $this->getOwner(),
            $this->getDescription(),
            $this->getSubTasks()
            ->filter(fn ($task) => $task && $task instanceof self)
            ->map(fn (self $task) => new TaskDTO($task->getId(), $task->getName(), $task->getOwner(), $task->getDescription(), completedAt: $task->getCompletedAt())),
            $this->getCompletedAt(),
        );
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }
}
