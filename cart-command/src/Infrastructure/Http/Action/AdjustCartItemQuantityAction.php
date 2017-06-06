<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Infrastructure\Http\Action;

use Broadway\CommandHandling\CommandBus;
use Pamil\CartCommand\Application\Command\AdjustCartItemQuantity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdjustCartItemQuantityAction
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request, string $cartId): Response
    {
        $content = json_decode($request->getContent(), true);

        if (!isset($content['cartItemId'], $content['quantity'])) {
            return new JsonResponse(['error' => 'Invalid request content'], 400);
        }

        $this->commandBus->dispatch(new AdjustCartItemQuantity($cartId, $content['cartItemId'], $content['quantity']));

        return new JsonResponse(null, 204);
    }
}
