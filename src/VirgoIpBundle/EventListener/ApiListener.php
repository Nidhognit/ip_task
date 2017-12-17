<?php

namespace VirgoIpBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use VirgoIpBundle\Exceptions\APIExceptionInterface;

class ApiListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 1],
            ],
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof APIExceptionInterface) {
            $message = $exception->getMessage();
        } else {
            $message = 'Unknown error';
        }

        $response = new JsonResponse(['errorMessage' => $message], 500);
        $event->setResponse($response);
    }
}