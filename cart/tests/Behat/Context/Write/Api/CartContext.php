<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Write\Api;

use Behat\Behat\Context\Context;
use Broadway\EventStore\EventStore;
use Pamil\BroadwayScenarioHelper\Scenario\Write\InfrastructureWriteScenario;
use Tests\Pamil\Cart\Behat\Storage\SharedStorage;
use Pamil\Cart\Write\Domain\Model\CartId;
use Symfony\Bundle\FrameworkBundle\Client;

final class CartContext implements Context
{
    /** @var SharedStorage  */
    private $sharedStorage;

    /** @var Client */
    private $client;

    public function __construct(SharedStorage $sharedStorage, EventStore $eventStore, Client $client)
    {
        $this->sharedStorage = $sharedStorage;
        $this->sharedStorage->define('scenario',  new InfrastructureWriteScenario($eventStore));

        $this->client = $client;
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
                $this->client->request('POST', '/' . $cartId);
            })
        ;
    }

    /**
     * @When I try to pick that cart up again
     */
    public function tryToPickUpCart(): void
    {
        $this->scenario()
            ->when(function (string $cartId) {
                $this->client->request('POST', '/' . $cartId);
            })
        ;
    }

    /**
     * @When I add :number :cartItemId cart items to that cart
     * @When I try to add :number :cartItemId cart items to that cart
     */
    public function addCartItem(int $number, string $cartItemId): void
    {
        $this->scenario()->when(function (string $cartId) use ($number, $cartItemId) {
            $this->client->request('POST', '/' . $cartId . '/items', [], [], [], json_encode([
                'cartItemId' => $cartItemId,
                'quantity' => $number,
            ]));
        });
    }

    /**
     * @When I adjust :cartItemId cart item quantity to :number
     */
    public function adjustCartItemQuantity(string $cartItemId, int $number): void
    {
        $this->scenario()->when(function (string $cartId) use ($cartItemId, $number) {
            $this->client->request('PUT', '/' . $cartId . '/items', [], [], [], json_encode([
                'cartItemId' => $cartItemId,
                'quantity' => $number,
            ]));
        });
    }

    /**
     * @When I remove :cartItemId cart item from the cart
     */
    public function removeCartItem(string $cartItemId): void
    {
        $this->scenario()->when(function (string $cartId) use ($cartItemId) {
            $this->client->request('DELETE', '/' . $cartId . '/items', [], [], [], json_encode([
                'cartItemId' => $cartItemId,
            ]));
        });
    }

    private function scenario(): InfrastructureWriteScenario
    {
        return $this->sharedStorage->get('scenario');
    }
}
