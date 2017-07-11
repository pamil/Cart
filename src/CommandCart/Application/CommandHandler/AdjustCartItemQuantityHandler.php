<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\CommandCart\Application\Command\AdjustCartItemQuantity;
use Pamil\CommandCart\Application\Exception\CartNotFoundException;
use Pamil\CommandCart\Application\Repository\CartRepository;
use Pamil\CommandCart\Domain\Model\Quantity;

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
