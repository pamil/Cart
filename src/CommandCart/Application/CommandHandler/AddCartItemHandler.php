<?php

declare(strict_types=1);

namespace Pamil\CommandCart\Application\CommandHandler;

use Broadway\CommandHandling\CommandHandler;
use Pamil\CommandCart\Application\Command\AddCartItem;
use Pamil\CommandCart\Application\Exception\CartNotFoundException;
use Pamil\CommandCart\Application\Repository\CartRepository;
use Pamil\CommandCart\Domain\Model\Quantity;
use Pamil\CommandCart\Domain\Repository\ProductCatalogue;

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
