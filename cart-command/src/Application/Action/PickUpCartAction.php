<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Application\Action;

use Broadway\CommandHandling\CommandBus;
use Pamil\CartCommand\Application\Command\PickUpCart;
use Pamil\CartCommand\Application\Exception\CartAlreadyPickedUpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class PickUpCartAction
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(string $cartId): Response
    {
        try {
            $this->commandBus->dispatch(new PickUpCart($cartId));

            return new JsonResponse(null, 204);
        } catch (CartAlreadyPickedUpException $exception) {
            return new JsonResponse(['error' => 'Cart was already picked up'], 409);
        }
    }
}
