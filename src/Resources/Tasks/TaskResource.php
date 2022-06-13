<?php

namespace App\Resources\Tasks;

use App\DTO\TaskDTO;
use Doctrine\Common\Collections\ArrayCollection;

class TaskResource
{

    public function toResourceArray(TaskDTO $taskDTO): array
    {
        return [
            ...$this->taskBaseData($taskDTO->id, $taskDTO->name, $taskDTO->description),
            'tasks' => $taskDTO->subTasks->map(fn (TaskDto $subTask) => $this->taskBaseData($subTask->id, $subTask->name, $subTask->description))->toArray(),
        ];
    }

    public function toResourcesArray(TaskDTO ...$tasks): array
    {
        return (new ArrayCollection($tasks))
        ->map(fn (TaskDTO $task) => self::toResourceArray($task))
        ->toArray();
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
