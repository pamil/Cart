<?php

declare(strict_types=1);

namespace Pamil\CartCommand\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\CartCommand\Application\Command\PickUpCart;
use Pamil\CartCommand\Application\Exception\CartAlreadyPickedUpException;
use Pamil\CartCommand\Application\Repository\CartRepository;
use Pamil\CartCommand\Domain\Model\Cart;
use Pamil\CartCommand\Domain\Model\CartId;

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

        $cart = Cart::pickUp(CartId::fromString($command->cartId()));

        $this->cartRepository->save($cart);
    }

    public function handle($command): void
    {
        $this($command);
    }
}
