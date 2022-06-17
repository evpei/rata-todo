<?php

namespace App\Requests;

use App\Exceptions\ValidationFailedHttpException;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

class ApiKeyRequest
{
    private Request $request;

    public function __construct(
        ?RequestStack $requestStack,
        private ValidatorInterface $validator,
        private UserRepository $userRepository,
    ) {
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Constraints\NotNull(message: 'Owner not found')]
    public function getOwner()
    {
        $apiKey = $this->request->headers->get('x-api-key');

        return $this->userRepository->findOneBy(['apiKey' => $apiKey]);
    }

    #[Required]
    public function validate(): void
    {
        $errors = $this->validator->validate($this);
        $errors->count() === 0 ?: $this->throwValidationException($errors);
    }

    protected function throwValidationException(ConstraintViolationListInterface $errors): never
    {
        throw new ValidationFailedHttpException($errors);
    }
}
