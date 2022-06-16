<?php

namespace App\Requests;

use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

class TaskRequest {

    private Request $request;

    public function __construct(
        ?RequestStack $requestStack,
        private ValidatorInterface $validator)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Constraints\Length(min: 2, max:20, minMessage: 'Name too short.', maxMessage: 'Name too long.')]
    public function getName() {
        return $this->request->toArray()['name'];
    }

    #[Constraints\Length(min: 2, max: 250, minMessage: 'Description too short.', maxMessage: 'Description too long')]
    public function getDescription() {
        return $this->request->toArray()['name'];    }

    #[Constraints\Length(min: 100, minMessage: 'Name too short.')]
    public function getParentTaskId() {
        return $this->request->attributes->get('parent_task_id');
    }

    #[Required]
    public function validate(): void {
        $errors = $this->validator->validate($this);
        $errors->count() === 0 ?: $this->throwValidationException($errors);
    }

    protected function throwValidationException(ConstraintViolationListInterface $errors): never {
        throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, (string) $errors);
    }
}