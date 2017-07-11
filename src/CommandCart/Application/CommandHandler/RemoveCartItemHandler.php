<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\CommandCart\Application\Command\RemoveCartItem;
use Pamil\CommandCart\Application\Exception\CartNotFoundException;
use Pamil\CommandCart\Application\Repository\CartRepository;

final class RemoveCartItemHandler implements CommandHandler
{
    /** @var CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /** @throws CartNotFoundException */
    public function __invoke(RemoveCartItem $command): void
    {
        $cart = $this->cartRepository->get($command->cartId());

        $cart->removeItem($command->productId());

        $this->cartRepository->save($cart);
    }

    public function handle($command): void
    {
        if (!$command instanceof RemoveCartItem) {
            return;
        }

        $this($command);
    }
}
