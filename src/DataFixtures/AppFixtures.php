<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $user = $this->createRandomUser($manager);
            $manager->persist($user);
            $manager->flush();

            for ($j = 0; $j < 5; $j++) {
                $task = $this->createRandomTaskFor($user);
                $manager->persist($task);
                $manager->flush();
                for ($k = 0; $k < 3; $k++) {
                    $subTask = $this->createRandomSubTaskFor($task);
                    $manager->persist($subTask);
                    $manager->flush();
                }
            }
        }
    }

    private function createRandomUser(): User
    {
        return $this->createUser($this->faker->email(), $this->faker->regexify('[A-Za-z0-9]{20}'));
    }

    private function createUser(string $email, string $apiKey, array $roles = []): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setApiKey($apiKey);
        $user->setRoles($roles);

        return $user;
    }

    private function createRandomTaskFor(User $user): Task
    {
        $task = new Task();
        $task->setOwner($user);
        $task->setName($this->faker->domainWord());
        
        if ($this->faker->boolean(80)) {
            $task->setDescription($this->faker->paragraph(3));
        }
        if ($this->faker->boolean(20)) {
            $task->setCompletedAt(
                new DateTimeImmutable($this->faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'))
            );
        }

        return $task;
    }

    private function createRandomSubTaskFor(Task $parentTask): Task
    {
        $task = $this->createRandomTaskFor($parentTask->getOwner());
        $task->setParentTask($parentTask);

        return $task;
    }
}
