<?php

namespace App\Resources\Tasks;

use App\DTO\TaskDTO;
use Doctrine\Common\Collections\ArrayCollection;

class TaskResource
{
    public function __construct(private array $resourceData = [])
    {}

    public function buildResourceArray(TaskDTO $taskDTO): void
    {
        $this->resourceData = [
            ...$this->taskBaseData($taskDTO->id, $taskDTO->name, $taskDTO->description),
            'tasks' => $taskDTO->subTasks->map(fn (TaskDto $subTask) => $this->taskBaseData($subTask->id, $subTask->name, $subTask->description))->toArray(),
        ];
    }

    private function getResourceArray(TaskDTO $taskDTO): array
    {
        return [
            ...$this->taskBaseData($taskDTO->id, $taskDTO->name, $taskDTO->description),
            'tasks' => $taskDTO->subTasks->map(fn (TaskDto $subTask) => $this->taskBaseData($subTask->id, $subTask->name, $subTask->description))->toArray(),
        ];
    }

    public function buildResourcesArray(TaskDTO ...$tasks): void
    {
        $this->resourceData = (new ArrayCollection($tasks))
        ->map(fn (TaskDTO $task) => $this->getResourceArray($task))
        ->toArray();
    }

    public function getResourceData(): array {
        return $this->resourceData;
    }

    private function taskBaseData(int $id, string $name, ?string $description): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'description' => $description,
        ];
    }
}
