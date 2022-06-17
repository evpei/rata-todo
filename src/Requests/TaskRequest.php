<?php

namespace App\Requests;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskRequest extends ApiKeyRequest 
{
    private Request $request;

    public function __construct(
        ?RequestStack $requestStack,
        private ValidatorInterface $validator,
        private UserRepository $userRepository,
    ) {
        $this->request = $requestStack->getCurrentRequest();
        parent::__construct($requestStack, $validator, $userRepository);
    }

    #[Constraints\NotBlank(message: 'Name cannot be blank.')]
    #[Constraints\Length(min: 2, max: 255, minMessage: 'Name too short. Cannot be shorter than {{ limit }} ', maxMessage: 'Name too long. Cannot be longer than {{ limit }}')]
    #[Constraints\Type(type: 'string', message:'Name must be a string.')]
    public function getName()
    {
        return $this->request->toArray()['name'] ?? null;
    }

    #[Constraints\Length(max: 255, maxMessage: 'Description too long')]
    #[Constraints\Type(type: 'string', message:'Description must be a string.')]
    public function getDescription()
    {
        return $this->request->toArray()['description'] ?? null;
    }

    #[Constraints\Positive(message: 'ParentTaskId must be positive')]
    #[Constraints\Type(type: 'integer', message: ' {{ value }} must be an integer.')]
    public function getParentTaskId()
    {
        return $this->request->toArray()['parent_task_id'] ?? null;
    }

    #[Constraints\DateTime(message: '{{ value }} must be a DateTime.')]
    public function getCompletedAt()
    {
        return $this->request->toArray()['completed_at'] ?? null;
    }
    
}
