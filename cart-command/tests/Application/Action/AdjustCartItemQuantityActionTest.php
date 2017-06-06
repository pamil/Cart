<?php

declare(strict_types=1);

namespace Tests\Pamil\CartCommand\Application\Action;

use Pamil\CartCommand\Application\Command\AddCartItem;
use Pamil\CartCommand\Application\Command\PickUpCart;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

final class AdjustCartItemQuantityActionTest extends WebTestCase
{
    /** {@inheritdoc} */
    protected function setUp(): void
    {
        (new Application(self::createKernel()))->find('broadway:event-store:schema:drop')->run(new StringInput(''), new NullOutput());
        (new Application(self::createKernel()))->find('broadway:event-store:schema:init')->run(new StringInput(''), new NullOutput());
    }

    /** @test */
    public function it_adjust_cart_item_quantity(): void
    {
        $client = static::createClient();

        $client->getContainer()->get('broadway.command_handling.command_bus')->dispatch(new PickUpCart('457e2ac8-8daf-47aa-a703-39b42d7f82ce'));
        $client->getContainer()->get('broadway.command_handling.command_bus')->dispatch(new AddCartItem('457e2ac8-8daf-47aa-a703-39b42d7f82ce', 'Fallout', 3));

        $client->request('PUT', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([
            'cartItemId' => 'Fallout',
            'quantity' => 5,
        ]));

        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('', $response->getContent());
    }

    /** @test */
    public function it_fails_while_trying_to_add_cart_item_to_unexisting_cart(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([
            'cartItemId' => 'Fallout',
            'quantity' => 5,
        ]));

        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('{"error":"Cart has not been picked up before"}', $response->getContent());
    }

    /** @test */
    public function it_fails_while_passing_invalid_request_content(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([]));

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"error":"Invalid request content"}', $response->getContent());
    }
}
