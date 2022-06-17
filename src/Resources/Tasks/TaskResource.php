<?php

namespace App\Resources\Tasks;

use App\Contracts\JsonResource;
use App\DTO\TaskDTO;
use DateTimeImmutable;

class TaskResource implements JsonResource
{
    public function __construct(private TaskDTO $taskDTO)
    {}

    public function toArray(): array
    {
        return [
            ...$this->taskBaseData($this->taskDTO->id, $this->taskDTO->name, $this->taskDTO->description),
            'tasks' => $this->taskDTO->subTasks->map(fn (TaskDto $subTask) => $this->taskBaseData($subTask->id, $subTask->name, $subTask->description))->toArray(),
        ];
    }
    
    private function taskBaseData(int $id, string $name, ?string $description, ?DateTimeImmutable $completedAt = null): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'completed_at' => is_null($completedAt) ? null : $completedAt->format('Y-m-d H:i:s'),
        ];
    }
}
