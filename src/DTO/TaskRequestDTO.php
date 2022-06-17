<?php

namespace App\DTO;

use App\Contracts\DTO;
use App\Entity\User;
use App\Requests\TaskRequest;
use DateTimeImmutable;

class TaskRequestDTO implements DTO
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly ?int $parentTaskId;
    public readonly User $owner;
    public readonly ?DateTimeImmutable $completedAt;

    public function __construct(TaskRequest $request) {
        $this->name = $request->getName();
        $this->description = $request->getDescription();
        $this->parentTaskId = $request->getParentTaskId();
        $this->owner = $request->getOwner();
        $this->completedAt = new DateTimeImmutable($request->getCompletedAt());
    }
}
