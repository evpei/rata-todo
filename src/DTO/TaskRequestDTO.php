<?php

namespace App\DTO;

use App\Contracts\DTO;
use App\Requests\TaskRequest;

class TaskRequestDTO implements DTO
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly ?int $parentTaskId;

    public function __construct(TaskRequest $request) {
        $this->name = $request->getName();
        $this->description = $request->getDescription();
        $this->parentTaskId = $request->getParentTaskId();
    }
}
