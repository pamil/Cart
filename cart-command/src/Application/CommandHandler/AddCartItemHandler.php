<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\CartCommand\Application\Command\AddCartItem;
use Pamil\CartCommand\Application\Exception\CartNotFoundException;
use Pamil\CartCommand\Application\Repository\CartRepository;
use Pamil\CartCommand\Domain\Model\Quantity;

final class AddCartItemHandler implements CommandHandler
{
    /** @var CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /** @throws CartNotFoundException */
    public function __invoke(AddCartItem $command): void
    {
        $cart = $this->cartRepository->get($command->cartId());

        $cart->addItem($command->cartItemId(), new Quantity($command->quantity()));

        $this->cartRepository->save($cart);
    }

    public function handle($command): void
    {
        if (!$command instanceof AddCartItem) {
            return;
        }

        $this($command);
    }
}
