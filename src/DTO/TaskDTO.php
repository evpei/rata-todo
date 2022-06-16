<?php

namespace App\DTO;

use App\Contracts\DTO;
use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Constraint;


class TaskDTO implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ArrayCollection $subTasks = new ArrayCollection,
    ) {
    }
}
