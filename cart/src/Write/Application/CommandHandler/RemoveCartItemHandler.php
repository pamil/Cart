<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\Cart\Write\Application\Command\RemoveCartItem;
use Pamil\Cart\Write\Application\Exception\CartNotFoundException;
use Pamil\Cart\Write\Application\Repository\CartRepository;

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

        $cart->removeItem($command->cartItemId());

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
