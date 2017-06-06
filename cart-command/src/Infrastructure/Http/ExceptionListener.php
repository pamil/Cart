<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Infrastructure\Http;

use Pamil\CartCommand\Application\Exception\CartAlreadyPickedUpException;
use Pamil\CartCommand\Application\Exception\CartNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

final class ExceptionListener
{
    /** @var int[] */
    private static $exceptionClassToResponseCode = [
        CartAlreadyPickedUpException::class => Response::HTTP_CONFLICT,
        CartNotFoundException::class => Response::HTTP_NOT_FOUND,
    ];

    public function __invoke(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $event->setResponse(new JsonResponse(
            ['error' => $exception->getMessage()],
            self::$exceptionClassToResponseCode[get_class($exception)] ?? Response::HTTP_INTERNAL_SERVER_ERROR
        ));
    }
}
