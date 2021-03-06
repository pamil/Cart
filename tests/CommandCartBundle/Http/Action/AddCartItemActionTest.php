<?php

declare(strict_types=1);

namespace Tests\Pamil\CommandCartBundle\Http\Action;

use Pamil\Cart\Domain\Event\CartPickedUp;
use Pamil\Cart\Domain\Event\ProductAddedToCatalogue;
use Pamil\CommandCart\Application\Command\AddCartItem;
use Pamil\CommandCart\Application\Command\PickUpCart;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

final class AddCartItemActionTest extends WebTestCase
{
    /** {@inheritdoc} */
    protected function setUp(): void
    {
        (new Application(self::createKernel()))->find('broadway:event-store:schema:drop')->run(new StringInput(''), new NullOutput());
        (new Application(self::createKernel()))->find('broadway:event-store:schema:init')->run(new StringInput(''), new NullOutput());
    }

    /** @test */
    public function it_adds_cart_item_to_picked_up_cart(): void
    {
        $client = static::createClient();

        $client->getContainer()->get('broadway.event_handling.event_bus')->dispatch(new ProductAddedToCatalogue('Fallout'));
        $client->getContainer()->get('broadway.event_handling.event_bus')->dispatch(new CartPickedUp('457e2ac8-8daf-47aa-a703-39b42d7f82ce'));

        $client->request('POST', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([
            'productId' => 'Fallout',
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

        $client->request('POST', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([
            'productId' => 'Fallout',
            'quantity' => 5,
        ]));

        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('{"error":"Cart with ID \"457e2ac8-8daf-47aa-a703-39b42d7f82ce\" could not be found!"}', $response->getContent());
    }

    /** @test */
    public function it_fails_while_trying_to_add_more_than_three_different_cart_items(): void
    {
        $client = static::createClient();

        $client->getContainer()->get('broadway.command_handling.command_bus')->dispatch(new PickUpCart('457e2ac8-8daf-47aa-a703-39b42d7f82ce'));
        $client->getContainer()->get('broadway.command_handling.command_bus')->dispatch(new AddCartItem('457e2ac8-8daf-47aa-a703-39b42d7f82ce', 'Bloodborne', 3));
        $client->getContainer()->get('broadway.command_handling.command_bus')->dispatch(new AddCartItem('457e2ac8-8daf-47aa-a703-39b42d7f82ce', 'Baldur\'s gate', 7));
        $client->getContainer()->get('broadway.command_handling.command_bus')->dispatch(new AddCartItem('457e2ac8-8daf-47aa-a703-39b42d7f82ce', 'Don\'t Starve', 5));

        $client->request('POST', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([
            'productId' => 'Fallout',
            'quantity' => 2,
        ]));

        $response = $client->getResponse();

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('{"error":"Cart with ID \"457e2ac8-8daf-47aa-a703-39b42d7f82ce\" has reached its items limit!"}', $response->getContent());
    }

    /** @test */
    public function it_fails_while_passing_invalid_request_content(): void
    {
        $client = static::createClient();

        $client->request('POST', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([]));

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"error":"The required option \"productId\" is missing."}', $response->getContent());
    }
}
