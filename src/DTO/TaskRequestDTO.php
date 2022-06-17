<?php

namespace App\DTO;

use App\Contracts\DTO;
use App\Entity\Task;
use App\Entity\User;
use App\Requests\TaskRequest;
use DateTimeImmutable;

class TaskRequestDTO implements DTO
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly ?Task $parentTask;
    public readonly User $owner;
    public readonly ?DateTimeImmutable $completedAt;

    public function __construct(TaskRequest $request) {
        $this->name = $request->getName();
        $this->description = $request->getDescription();
        $this->parentTask = $request->getParentTask();
        $this->owner = $request->getOwner();
        $this->completedAt = $request->getCompletedAt() !== null ? new DateTimeImmutable($request->getCompletedAt()) : null;
    }
}
