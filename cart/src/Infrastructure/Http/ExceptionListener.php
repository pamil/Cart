<?php

declare(strict_types=1);

namespace Pamil\Cart\Infrastructure\Http;

use Pamil\Cart\Application\Exception\CartAlreadyPickedUpException;
use Pamil\Cart\Application\Exception\CartNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;

final class ExceptionListener
{
    /** @var int[] */
    private static $throwableToResponseCode = [
        CartAlreadyPickedUpException::class => Response::HTTP_CONFLICT,
        CartNotFoundException::class => Response::HTTP_NOT_FOUND,
        ExceptionInterface::class => Response::HTTP_BAD_REQUEST,
    ];

    public function __invoke(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        $event->setResponse(JsonResponse::fromJsonString(
            json_encode(['error' => $exception->getMessage()]),
            $this->getResponseCode($exception)
        ));
    }

    private function getResponseCode(\Throwable $throwable): int
    {
        foreach (static::$throwableToResponseCode as $throwable2 => $responseCode) {
            if ($throwable instanceof $throwable2) {
                return $responseCode;
            }
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}