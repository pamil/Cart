<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Infrastructure\Http\Action;

use Broadway\CommandHandling\CommandBus;
use Pamil\Cart\Write\Application\Command\AdjustCartItemQuantity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AdjustCartItemQuantityAction
{
    /** @var CommandBus */
    private $commandBus;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        $this->optionsResolver = (new OptionsResolver())
            ->setRequired('productId')
            ->setDefault('quantity', 1)
            ->setAllowedTypes('quantity', 'int')
        ;
    }

    public function __invoke(Request $request, string $cartId): Response
    {
        /** @var array $content */
        $content = $this->optionsResolver->resolve(json_decode($request->getContent(), true) ?: []);

        $this->commandBus->dispatch(new AdjustCartItemQuantity($cartId, $content['productId'], $content['quantity']));

        return new JsonResponse(null, 204);
    }
}
