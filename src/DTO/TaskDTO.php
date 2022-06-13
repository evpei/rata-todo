<?php

namespace App\DTO;

use App\Contracts\DTO;
use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;

class TaskDTO implements DTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $id = null,
        public readonly string $description = '',
        public readonly ArrayCollection $subTasks = new ArrayCollection,
        public readonly ?int $parentTaskId = null,
    ) {
    }

    public static function fromEntity(Task $task): self
    {
        return new self(
            $task->getName(),
            $task->getId(),
            $task->getDescription(),
            $task->getSubTasks()->map(
                fn (Task $task) => new self($task->getName(), $task->getId(), $task->getDescription())
            )
        );
    }
}
