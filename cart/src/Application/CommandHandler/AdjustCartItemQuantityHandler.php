<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\Cart\Application\Command\AdjustCartItemQuantity;
use Pamil\Cart\Application\Exception\CartNotFoundException;
use Pamil\Cart\Application\Repository\CartRepository;
use Pamil\Cart\Domain\Model\Quantity;

final class AdjustCartItemQuantityHandler implements CommandHandler
{
    /** @var CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /** @throws CartNotFoundException */
    public function __invoke(AdjustCartItemQuantity $command): void
    {
        $cart = $this->cartRepository->get($command->cartId());

        $cart->adjustItemQuantity($command->cartItemId(), new Quantity($command->quantity()));

        $this->cartRepository->save($cart);
    }

    public function handle($command): void
    {
        if (!$command instanceof AdjustCartItemQuantity) {
            return;
        }

        $this($command);
    }
}
