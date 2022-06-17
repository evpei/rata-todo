<?php

namespace App\Resources\Tasks;

use App\Contracts\JsonResource;
use App\DTO\TaskDTO;
use Doctrine\Common\Collections\ArrayCollection;

class TaskCollectionResource implements JsonResource
{
    /** @var TaskDTO[] $tasks */
    private array $tasks;
    
    public function __construct(TaskDTO ...$tasks)
    {
        $this->tasks = $tasks;
    }

    public function toArray(): array
    {
        return (new ArrayCollection($this->tasks))
        ->map(fn (TaskDTO $taskDTO) => (new TaskResource($taskDTO))->toArray())
        ->toArray();
    }
}
