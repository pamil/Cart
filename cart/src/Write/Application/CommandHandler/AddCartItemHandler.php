<?php

declare(strict_types=1);

namespace Pamil\Cart\Write\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\Cart\Write\Application\Command\AddCartItem;
use Pamil\Cart\Write\Application\Exception\CartNotFoundException;
use Pamil\Cart\Write\Application\Repository\CartRepository;
use Pamil\Cart\Write\Domain\Model\Quantity;
use Pamil\Cart\Write\Domain\Repository\ProductCatalogue;

final class AddCartItemHandler implements CommandHandler
{
    /** @var CartRepository */
    private $cartRepository;

    /** @var ProductCatalogue */
    private $productCatalogue;

    public function __construct(CartRepository $cartRepository, ProductCatalogue $productCatalogue)
    {
        $this->cartRepository = $cartRepository;
        $this->productCatalogue = $productCatalogue;
    }

    /** @throws CartNotFoundException */
    public function __invoke(AddCartItem $command): void
    {
        $cart = $this->cartRepository->get($command->cartId());

        $cart->addItem($this->productCatalogue, $command->productId(), new Quantity($command->quantity()));

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
