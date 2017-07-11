<?php

declare(strict_types=1);

namespace Pamil\CommandCartBundle\Http\Action;

use Broadway\CommandHandling\CommandBus;
use Pamil\Cart\Write\Application\Command\RemoveCartItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RemoveCartItemAction
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
        ;
    }

    public function __invoke(Request $request, string $cartId): Response
    {
        /** @var array $content */
        $content = $this->optionsResolver->resolve(json_decode($request->getContent(), true) ?: []);

        $this->commandBus->dispatch(new RemoveCartItem($cartId, $content['productId']));

        return new JsonResponse(null, 204);
    }
}
