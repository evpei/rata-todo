<?php

namespace App\Repository;

use App\DTO\TaskDTO;
use App\DTO\TaskRequestDTO;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

    public function store(TaskRequestDTO $taskDTO): TaskDTO {
        $task = new Task(['name' => $taskDTO->name, 'description' => $taskDTO->description, 'parent_task_id' => $taskDTO->parentTaskId]);

        $this->getEntityManager()->persist($task);

        $this->getEntityManager()->flush();

        return $task->toDTO();
    }

    public function update(TaskRequestDTO $taskDTO): TaskDTO {
        $task = new Task(['name' => $taskDTO->name, 'description' => $taskDTO->description, 'parent_task_id' => $taskDTO->parentTaskId]);

        $this->getEntityManager()->persist($task);

        $this->getEntityManager()->flush();

        return $task->toDTO();
    }

    public function delete(Task $task): void {

        $this->getEntityManager()->remove($task);

        $this->getEntityManager()->flush();
    }



    /**
     * Returns all the subtasks
     * 
     * Todo: The Tasks must be associated to an user entity
     *
     * @return TaskDto[]
     */
    public function getWithSubTask(): array {
        return (new ArrayCollection($this->createQueryBuilder('tasks')
        ->where('tasks.parentTask IS NULL')
        ->getQuery()
        ->getResult()))
        ->filter(fn ($task) => $task instanceof Task)
        ->map(fn (Task $task) => $task->toDTO())
        ->toArray();
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
