<?php

namespace App\Controller;

use App\Contracts\DTO;
use App\DTO\TaskDTO;
use App\Entity\Task;
use App\Enums\ResponseError;
use App\Repository\TaskRepository;
use App\Resources\Tasks\TaskResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('task', name: 'task.')]
class TaskController extends AbstractController
{

    public function __construct(
        private TaskRepository $taskRepository,
        private TaskResource $taskResource,
    ) {
    }

    #[Route('', name: 'index', methods: 'GET')]
    public function index(): JsonResponse
    {
        $tasks = $this->taskRepository
        ->getParentTasks()
        ->map(fn (Task $task) => TaskDTO::fromEntity($task))
        ->toArray();

        return $this->json([
            'data' => $this->taskResource->toResourcesArray(...$tasks),
        ], Response::HTTP_OK);
    }

    #[Route('/{taskId}', name: 'show', methods: 'GET', requirements: ["taskId" => "\d+"])]
    public function show(int $taskId): JsonResponse
    {
        $task = $this->taskRepository->findOneBy(['id' => $taskId]);

        if (!$task) {
            return $this->jsonError(ResponseError::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $this->taskResource->toResourceArray(TaskDto::fromEntity($task))]);
    }

    #[Route('', name: 'store', methods: 'POST')]
    public function store(Request $request): JsonResponse
    {
        return $this->json($request->toArray());
        if(! $request->attributes->get('name')) {
            return $this->jsonError(ResponseError::HTTP_BAD_REQUEST);
        }

        $task = new TaskDTO(name: $request->get('name'), description: $request->get('description'), parentTaskId: $request->get('parentTaskId'));
        $storedTask = TaskDTO::fromEntity($this->taskRepository->store($task));
        
        return $this->json($this->taskResource->toResourceArray($storedTask), Response::HTTP_CREATED);
    }

    #[Route('/{taskId}', name: 'update', methods: 'PUT', requirements: ['taskId' => '\d+'])]
    public function update(int $taskId, Request $request): JsonResponse
    {
        $task = $this->taskRepository->findOneBy(['id' => $taskId]);

        if (!$task) {
            return $this->jsonError(ResponseError::HTTP_NOT_FOUND);
        }

        return $this->json([
            'name' => 'Welcome to your new controller!',
        ], Response::HTTP_OK);
    }

    #[Route('/{taskId}', name: 'delete', methods: 'DELETE', requirements: ["taskId" => "\d+"])]
    public function delete(int $taskId): JsonResponse
    {
        $task = $this->taskRepository->findOneBy(['id' => $taskId]);

        if (!$task) {
            throw $this->createNotFoundException();
            return $this->jsonError(ResponseError::HTTP_NOT_FOUND);
        }

        return $this->json([
            'name' => 'Welcome to your new controller!',
        ], Response::HTTP_OK);
    }

    private function jsonError(ResponseError $responseError): JsonResponse
    {
        return $this->json(['error' => [
            'message' => $responseError->getMessage(),
            'code' => $responseError->getStatusCode(),
        ]], $responseError->getStatusCode());
    }
}
