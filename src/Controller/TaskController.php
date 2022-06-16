<?php

namespace App\Controller;

use App\DTO\TaskRequestDTO;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Resources\Tasks\TaskResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('task', name: 'task.')]
class TaskController extends AbstractController
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskResource $taskResource,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'index', methods: 'GET')]
    public function index(): JsonResponse
    {
        $this->taskResource->buildResourcesArray(...$this->taskRepository->getWithSubTask());

        return $this->taskResponse(Response::HTTP_OK);
    }

    #[Route('/{task}', name: 'show', methods: 'GET', requirements: ["taskId" => "\d+"])]
    public function show(Task $task): JsonResponse
    {
        $this->taskResource->buildResourceArray($task->toDTO());

        return $this->taskResponse(Response::HTTP_OK);
    }

    #[Route('', name: 'store', methods: 'POST')]
    public function store(TaskRequestDTO $taskRequestDto): JsonResponse
    {
        $this->taskResource->buildResourceArray($this->taskRepository->store($taskRequestDto));

        return $this->taskResponse(Response::HTTP_CREATED);
    }

    #[Route('/{task}', name: 'update', methods: 'PUT',)]
    public function update(Task $task, TaskRequestDTO $updateTaskDto): JsonResponse
    {
        $this->taskResource->buildResourceArray($task->toDTO());

        return $this->taskResponse(Response::HTTP_OK);
    }

    #[Route('/{task}', name: 'delete', methods: 'DELETE', requirements: ["taskId" => "\d+"])]
    public function delete(Task $task): JsonResponse
    {
        $this->taskRepository->delete($task);

        return $this->taskResponse(Response::HTTP_NO_CONTENT);
    }

    private function taskResponse(int $statusCode = Response::HTTP_OK): JsonResponse {
        return $this->json([$this->taskResource->getResourceData()], $statusCode);
    }
}
