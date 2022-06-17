<?php

namespace App\Controller;

use App\DTO\TaskRequestDTO;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Requests\ApiKeyRequest;
use App\Resources\Tasks\TaskCollectionResource;
use App\Resources\Tasks\TaskResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('task', name: 'task.')]
class TaskController extends ApiController
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {
    }

    /** TODO: Add Pagination */
    #[Route('', name: 'index', methods: 'GET')]
    public function index(ApiKeyRequest $request): JsonResponse
    {
        $userTasks = $request->getOwner()
            ->getTasks()
            ->map(fn (Task $task) => $task->toDTO());

        return $this->formattedJsonResponse(new TaskCollectionResource(...$userTasks));
    }

    #[Route('/{task}', name: 'show', methods: 'GET', requirements: ["taskId" => "\d+"])]
    public function show(Task $task, ApiKeyRequest $request): JsonResponse
    {
        $this->authorizeAccess($request->getOwner(), $task);

        return $this->formattedJsonResponse(new TaskResource($task->toDTO()));
    }

    #[Route('', name: 'store', methods: 'POST')]
    public function store(TaskRequestDTO $taskRequestDto): JsonResponse
    {
        if($taskRequestDto->parentTaskId) {
            $this->authorizeParentTaskAccess($taskRequestDto->owner, $taskRequestDto->parentTaskId);
        }

        $taskDTO = $this->taskRepository->store($taskRequestDto);

        return $this->formattedJsonResponse(new TaskResource($taskDTO), Response::HTTP_CREATED);
    }

    #[Route('/{task}', name: 'update', methods: 'PUT',)]
    public function update(Task $task, TaskRequestDTO $taskRequestDto): JsonResponse
    {
        $this->authorizeAccess($taskRequestDto->owner, $task);

        if($taskRequestDto->parentTaskId) {
            $this->authorizeParentTaskAccess($taskRequestDto->owner, $taskRequestDto->parentTaskId);
        }

        $updatedTaskDTO = $this->taskRepository->update($task, $taskRequestDto);

        return $this->formattedJsonResponse(new TaskResource($updatedTaskDTO), Response::HTTP_OK);
    }

    #[Route('/{task}', name: 'delete', methods: 'DELETE')]
    public function delete(Task $task, ApiKeyRequest $request): JsonResponse
    {
        $this->authorizeAccess($request->getOwner(), $task);

        $this->taskRepository->delete($task);

        return $this->formattedJsonResponse(statusCode: Response::HTTP_NO_CONTENT);
    }

    /**
     * Authorizes the ability of the user to interact with the Entity
     *
     * @param User $user the current user
     * @param Task $task the Entity with which the user wants to interact
     * @throws Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @return void
     */
    protected function authorizeAccess(User $user, Task $task): void
    {
        if (!$user->getId() || $user->getId() !== $task->getOwner()->getId()) {
            throw $this->createAccessDeniedHttpException('User is not authorized to view this resource.');
        }
    }

    protected function authorizeParentTaskAccess(User $user, int $parentTaskId): void
    {
        if(!$parentTask = $this->taskRepository->find($parentTaskId)) {
            throw $this->throwValidationException('ParentTask cannot be found.');
        }

        $this->authorizeAccess($user, $parentTask);
    }
}
