<?php

declare(strict_types=1);

namespace Pamil\CommandCartBundle\Http\Action;

use Broadway\CommandHandling\CommandBus;
use Pamil\CommandCart\Application\Command\PickUpCart;
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
        $this->commandBus->dispatch(new PickUpCart($cartId));

        return new JsonResponse(null, 204);
    }
}
