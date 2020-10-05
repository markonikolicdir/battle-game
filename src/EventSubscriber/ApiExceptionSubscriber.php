<?php

namespace App\EventSubscriber;

use App\Api\ApiProblemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ApiProblemException) {
            $apiProblem = $exception->getApiProblem();

            $response = new JsonResponse(
                $apiProblem->toArray(),
                $apiProblem->getStatusCode()
            );

            $response->headers->set('Content-Type', 'application/problem+json');
            $event->setResponse($response);
        }

    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
