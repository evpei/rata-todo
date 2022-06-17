<?php

namespace App\Repository;

use App\DTO\TaskDTO;
use App\DTO\TaskRequestDTO;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function add(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function store(TaskRequestDTO $taskRequestDTO): TaskDTO {
        $task = new Task();
        $this->fillFromTaskRequestDto($task, $taskRequestDTO);

        $this->getEntityManager()->persist($task);

        $this->getEntityManager()->flush();

        return $task->toDTO();
    }

    private function fillFromTaskRequestDto(Task $task, TaskRequestDTO $taskRequestDTO): void {
        $task->setName($taskRequestDTO->name);
        $task->setDescription($taskRequestDTO->description);
        $task->setParentTask($taskRequestDTO->parentTask);
        $task->setOwner($taskRequestDTO->owner);
        $task->setCompletedAt($taskRequestDTO->completedAt);
    }

    public function update(Task $task, TaskRequestDTO $taskRequestDTO): TaskDTO {

        $this->fillFromTaskRequestDto($task, $taskRequestDTO);
        $this->getEntityManager()->flush();

        return $task->toDTO();
    }

    public function delete(Task $task): void {

       foreach ($task->getSubTasks() as $subTask) {
            $this->delete($subTask);
        }
        $this->getEntityManager()->remove($task);

        $this->getEntityManager()->flush();
    }
}
