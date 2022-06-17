<?php

namespace App\EventSubscriber;

use App\Contracts\TokenAuthenticatedController;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class TokenSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedController) {
            $apiKey = $event->getRequest()->headers->get('x-api-key');
            $user = $this->userRepository->findOneBy(['apiKey' => $apiKey]);

            if (!$user) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Api-Key invalid.');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
