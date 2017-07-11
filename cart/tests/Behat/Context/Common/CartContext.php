<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Common;

use Behat\Behat\Context\Context;
use Pamil\BroadwayScenarioHelper\Scenario\Scenario;
use Tests\Pamil\Cart\Behat\Storage\SharedStorage;
use Pamil\Cart\Common\Domain\Event\CartItemAdded;
use Pamil\Cart\Common\Domain\Event\CartPickedUp;
use Pamil\Cart\Write\Domain\Model\CartId;

final class CartContext implements Context
{
    /** @var SharedStorage */
    private $sharedStorage;

    public function __construct(SharedStorage $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given the cart was picked up
     */
    public function cartPickedUp(): void
    {
        $cartId = CartId::generate();

        $this->scenario()
            ->withAggregateId($cartId->toString())
            ->given(new CartPickedUp($cartId->toString()))
        ;
    }

    /**
     * @Given :number :productId cart items were added to the cart
     */
    public function cartItemAdded(int $number, string $productId): void
    {
        $this->scenario()->given(new CartItemAdded($productId, $number));
    }

    private function scenario(): Scenario
    {
        return $this->sharedStorage->get('scenario');
    }
}
