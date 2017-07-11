<?php

declare(strict_types=1);

namespace Tests\Pamil\Behat\Context\Read\Api;

use Behat\Behat\Context\Context;
use Broadway\EventHandling\EventBus;
use Pamil\BroadwayScenarioHelper\Scenario\Read\InfrastructureReadScenario;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Tests\Pamil\Behat\Storage\SharedStorage;

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
        $this->sharedStorage->define('scenario', new InfrastructureReadScenario($eventBus));

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
        return $this->sharedStorage->get('scenario');
    }
}
