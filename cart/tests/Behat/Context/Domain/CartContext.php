<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Pamil\Cart\Write\Domain\Model\Cart;
use Pamil\Cart\Write\Domain\Model\CartId;
use Pamil\Cart\Write\Domain\Model\Quantity;
use Tests\Pamil\Cart\Behat\DomainScenario;
use Tests\Pamil\Cart\Behat\SharedStorage;

final class CartContext implements Context
{
    /** @var SharedStorage */
    private $sharedStorage;

    public function __construct(SharedStorage $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
        $this->sharedStorage->define('cart', new DomainScenario(Cart::class));
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
     * @When I add :number :cartItemId cart items to that cart
     */
    public function addCartItem(int $number, string $cartItemId): void
    {
        $this->scenario()->when(function (Cart $cart) use ($number, $cartItemId) {
            $cart->addItem($cartItemId, new Quantity($number));
        });
    }

    /**
     * @When I try to add :number :cartItemId cart items to that cart
     */
    public function tryToAddCartItem(int $number, string $cartItemId): void
    {
        try {
            $this->addCartItem($number, $cartItemId);
        } catch (\Throwable $throwable) {
            return;
        }
    }

    /**
     * @When I adjust :cartItemId cart item quantity to :number
     */
    public function adjustCartItemQuantity(string $cartItemId, int $number): void
    {
        $this->scenario()->when(function (Cart $cart) use ($cartItemId, $number) {
            $cart->adjustItemQuantity($cartItemId, new Quantity($number));
        });
    }

    /**
     * @When I remove :cartItemId cart item from the cart
     */
    public function removeCartItem(string $cartItemId): void
    {
        $this->scenario()->when(function (Cart $cart) use ($cartItemId) {
            $cart->removeItem($cartItemId);
        });
    }

    private function scenario(): DomainScenario
    {
        return $this->sharedStorage->get('cart');
    }
}
