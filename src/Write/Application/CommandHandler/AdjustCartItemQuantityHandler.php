<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\Cart\Write\Application\Command\AdjustCartItemQuantity;
use Pamil\Cart\Write\Application\Exception\CartNotFoundException;
use Pamil\Cart\Write\Application\Repository\CartRepository;
use Pamil\Cart\Write\Domain\Model\Quantity;

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

        $cart->adjustItemQuantity($command->productId(), new Quantity($command->quantity()));

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
