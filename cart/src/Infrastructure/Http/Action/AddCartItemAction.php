<?php

declare(strict_types=1);

namespace Pamil\Cart\Infrastructure\Http\Action;

use Broadway\CommandHandling\CommandBus;
use Pamil\Cart\Application\Command\AddCartItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddCartItemAction
{
    /** @var CommandBus */
    private $commandBus;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        $this->optionsResolver = (new OptionsResolver())
            ->setRequired('cartItemId')
            ->setDefault('quantity', 1)
            ->setAllowedTypes('quantity', 'int')
        ;
    }

    public function __invoke(Request $request, string $cartId): Response
    {
        /** @var array $content */
        $content = $this->optionsResolver->resolve(json_decode($request->getContent(), true) ?: []);

        $this->commandBus->dispatch(new AddCartItem($cartId, $content['cartItemId'], $content['quantity']));

        return new JsonResponse(null, 204);
    }
}
