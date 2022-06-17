<?php

namespace App\DTO;

use App\Contracts\DTO;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;


class TaskDTO implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly User $owner,
        public readonly ?string $description = null,
        public readonly ArrayCollection $subTasks = new ArrayCollection,
        public readonly ?DateTimeImmutable $completedAt = null,
    ) {
    }
}
