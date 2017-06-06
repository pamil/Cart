<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Infrastructure\Http\Action;

use Broadway\CommandHandling\CommandBus;
use Pamil\CartCommand\Application\Command\RemoveCartItem;
use Pamil\CartCommand\Application\Exception\CartNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RemoveCartItemAction
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

        if (!isset($content['cartItemId'])) {
            return new JsonResponse(['error' => 'Invalid request content'], 400);
        }

        try {
            $this->commandBus->dispatch(new RemoveCartItem($cartId, $content['cartItemId']));

            return new JsonResponse(null, 204);
        } catch (CartNotFoundException $exception) {
            return new JsonResponse(['error' => 'Cart has not been picked up before'], 404);
        }
    }
}
