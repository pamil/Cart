<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Write\Domain;

use Behat\Behat\Context\Context;
use Pamil\BroadwayScenarioHelper\Scenario\Write\DomainWriteScenario;
use Tests\Pamil\Cart\Behat\Storage\SharedStorage;
use Pamil\CommandCart\Domain\Model\Cart;
use Pamil\CommandCart\Domain\Model\CartId;
use Pamil\CommandCart\Domain\Model\Quantity;
use Pamil\CommandCart\Infrastructure\Repository\InMemoryProductCatalogue;
use Pamil\CommandCart\Infrastructure\Repository\ProductCatalogue;

final class CartContext implements Context
{
    /** @var SharedStorage */
    private $sharedStorage;

    /** @var ProductCatalogue */
    private $productCatalogue;

    public function __construct(SharedStorage $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
        $this->sharedStorage->define('scenario', new DomainWriteScenario(Cart::class));

        $this->productCatalogue = new InMemoryProductCatalogue();
    }

    /**
     * @Given product :product was added to the catalogue
     * @Given products :product1, :product2, :product3 and :product4 were added to the catalogue
     */
    public function productAddedToCatalogue(string ...$productsIds): void
    {
        foreach ($productsIds as $productId) {
            $this->productCatalogue->add($productId);
        }
    }

    /**
     * @When I pick up a cart
     */
    public function pickUpCart(): void
    {
        $cartId = CartId::generate();

        $this->scenario()
            ->withAggregateId($cartId->toString())
            ->when(function (string $cartId) {
                return Cart::pickUp(CartId::fromString($cartId));
            })
        ;
    }

    /**
     * @When I add :number :productId cart items to that cart
     */
    public function addCartItem(int $number, string $productId): void
    {
        $this->scenario()->when(function (Cart $cart) use ($number, $productId) {
            $cart->addItem($this->productCatalogue, $productId, new Quantity($number));
        });
    }

    /**
     * @When I try to add :number :productId cart items to that cart
     */
    public function tryToAddCartItem(int $number, string $productId): void
    {
        try {
            $this->addCartItem($number, $productId);
        } catch (\Throwable $throwable) {
            return;
        }
    }

    /**
     * @When I adjust :productId cart item quantity to :number
     */
    public function adjustCartItemQuantity(string $productId, int $number): void
    {
        $this->scenario()->when(function (Cart $cart) use ($productId, $number) {
            $cart->adjustItemQuantity($productId, new Quantity($number));
        });
    }

    /**
     * @When I remove :productId cart item from the cart
     */
    public function removeCartItem(string $productId): void
    {
        $this->scenario()->when(function (Cart $cart) use ($productId) {
            $cart->removeItem($productId);
        });
    }

    private function scenario(): DomainWriteScenario
    {
        return $this->sharedStorage->get('scenario');
    }
}
