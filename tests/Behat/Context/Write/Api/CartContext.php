<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Write\Api;

use Behat\Behat\Context\Context;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Pamil\BroadwayScenarioHelper\Scenario\Write\InfrastructureWriteScenario;
use Pamil\Cart\Common\Domain\Event\ProductAddedToCatalogue;
use Tests\Pamil\Cart\Behat\Storage\SharedStorage;
use Pamil\Cart\Write\Domain\Model\CartId;
use Pamil\Cart\Write\Infrastructure\Repository\ProductCatalogue;
use Symfony\Bundle\FrameworkBundle\Client;

final class CartContext implements Context
{
    /** @var SharedStorage  */
    private $sharedStorage;

    /** @var ProductCatalogue */
    private $productCatalogue;

    /** @var Client */
    private $client;

    public function __construct(
        SharedStorage $sharedStorage,
        EventStore $eventStore,
        ProductCatalogue $productCatalogue,
        Client $client
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->sharedStorage->define('scenario',  new InfrastructureWriteScenario($eventStore));

        $this->productCatalogue = $productCatalogue;
        $this->client = $client;
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
     * @When I add :number :productId cart items to that cart
     * @When I try to add :number :productId cart items to that cart
     */
    public function addCartItem(int $number, string $productId): void
    {
        $this->scenario()->when(function (string $cartId) use ($number, $productId) {
            $this->client->request('POST', '/' . $cartId . '/items', [], [], [], json_encode([
                'productId' => $productId,
                'quantity' => $number,
            ]));
        });
    }

    /**
     * @When I adjust :productId cart item quantity to :number
     */
    public function adjustCartItemQuantity(string $productId, int $number): void
    {
        $this->scenario()->when(function (string $cartId) use ($productId, $number) {
            $this->client->request('PUT', '/' . $cartId . '/items', [], [], [], json_encode([
                'productId' => $productId,
                'quantity' => $number,
            ]));
        });
    }

    /**
     * @When I remove :productId cart item from the cart
     */
    public function removeCartItem(string $productId): void
    {
        $this->scenario()->when(function (string $cartId) use ($productId) {
            $this->client->request('DELETE', '/' . $cartId . '/items', [], [], [], json_encode([
                'productId' => $productId,
            ]));
        });
    }

    private function scenario(): InfrastructureWriteScenario
    {
        return $this->sharedStorage->get('scenario');
    }
}
