<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\Cart\Write\Application\Command\PickUpCart;
use Pamil\Cart\Write\Application\Exception\CartAlreadyPickedUpException;
use Pamil\Cart\Write\Application\Repository\CartRepository;
use Pamil\Cart\Write\Domain\Model\Cart;
use Pamil\Cart\Write\Domain\Model\CartId;

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
        if (!$command instanceof PickUpCart) {
            return;
        }

        $this($command);
    }
}
