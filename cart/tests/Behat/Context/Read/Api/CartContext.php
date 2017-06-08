<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Behat\Context\Read\Api;

use Behat\Behat\Context\Context;
use Broadway\EventHandling\EventBus;
use Broadway\EventHandling\SimpleEventBus;
use Pamil\Cart\Common\Domain\Event\CartItemAdded;
use Pamil\Cart\Common\Domain\Event\CartPickedUp;
use Pamil\Cart\Read\Application\Projector\CartProjector;
use Pamil\Cart\Read\Domain\Model\Cart;
use Pamil\Cart\Read\Infrastructure\Repository\InMemoryCartRepository;
use Pamil\Cart\Write\Domain\Model\CartId;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Tests\Pamil\Cart\Behat\Scenario\Read\InfrastructureReadScenario;
use Tests\Pamil\Cart\Behat\SharedStorage;

final class CartContext implements Context
{
    /** @var SharedStorage */
    private $sharedStorage;

    /** @var Client */
    private $client;

    /** @var Response */
    private $response;

    public function __construct(SharedStorage $sharedStorage, EventBus $eventBus, Client $client)
    {
        $this->sharedStorage = $sharedStorage;
        $this->sharedStorage->define('cart', new InfrastructureReadScenario($eventBus));

        $this->client = $client;
    }

    /**
     * @When I ask for the cart details
     */
    public function iAskForTheCartDetails(): void
    {
        $this->scenario()->when(function (string $cartId): void {
            $this->client->request('GET', '/' . $cartId);

            $this->response = $this->client->getResponse();
        });
    }

    /**
     * @Then there should be :number cart items in the cart
     */
    public function thereShouldBeNumberOfCartItemsInTheCart(int $number): void
    {
        $this->scenario()->then(function () use ($number) : void {
            Assert::assertSame(200, $this->response->getStatusCode());

            $content = json_decode($this->response->getContent(), true);

            Assert::assertNotNull($content);
            Assert::assertArrayHasKey('items', $content);
            Assert::assertCount($number, $content['items']);
        });
    }

    private function scenario(): InfrastructureReadScenario
    {
        return $this->sharedStorage->get('cart');
    }
}
