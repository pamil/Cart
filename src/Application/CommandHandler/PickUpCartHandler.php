<?php

declare(strict_types=1);

namespace Pamil\Cart\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\Cart\Application\Command\PickUpCart;
use Pamil\Cart\Application\Repository\CartRepository;
use Pamil\Cart\Domain\Model\Cart;

final class PickUpCartHandler implements CommandHandler
{
    /** @var CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /** @throws CartAlreadyPickedUpException */
    public function __invoke(PickUpCart $command): void
    {
        if ($this->cartRepository->has($command->cartId())) {
            throw CartAlreadyPickedUpException::create($command->cartId());
        }

        $cart = Cart::pickUp($command->cartId());

        $this->cartRepository->save($cart);
    }

    public function handle($command): void
    {
        $this($command);
    }
}
