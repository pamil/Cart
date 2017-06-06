<?php

declare(strict_types=1);

namespace Tests\Pamil\Cart\Infrastructure\Http\Action;

use Pamil\Cart\Application\Command\AddCartItem;
use Pamil\Cart\Application\Command\PickUpCart;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

final class RemoveCartItemActionTest extends WebTestCase
{
    /** {@inheritdoc} */
    protected function setUp(): void
    {
        (new Application(self::createKernel()))->find('broadway:event-store:schema:drop')->run(new StringInput(''), new NullOutput());
        (new Application(self::createKernel()))->find('broadway:event-store:schema:init')->run(new StringInput(''), new NullOutput());
    }

    /** @test */
    public function it_removes_previously_added_cart_item(): void
    {
        $client = static::createClient();

        $client->getContainer()->get('broadway.command_handling.command_bus')->dispatch(new PickUpCart('457e2ac8-8daf-47aa-a703-39b42d7f82ce'));
        $client->getContainer()->get('broadway.command_handling.command_bus')->dispatch(new AddCartItem('457e2ac8-8daf-47aa-a703-39b42d7f82ce', 'Fallout', 3));

        $client->request('DELETE', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([
            'cartItemId' => 'Fallout',
        ]));

        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('', $response->getContent());
    }

    /** @test */
    public function it_fails_while_trying_to_remove_cart_item_from_unexisting_cart(): void
    {
        $client = static::createClient();

        $client->request('DELETE', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([
            'cartItemId' => 'Fallout',
        ]));

        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('{"error":"Cart with ID \"457e2ac8-8daf-47aa-a703-39b42d7f82ce\" could not be found!"}', $response->getContent());
    }

    /** @test */
    public function it_fails_while_passing_invalid_request_content(): void
    {
        $client = static::createClient();

        $client->request('DELETE', '/457e2ac8-8daf-47aa-a703-39b42d7f82ce/items', [], [], [], json_encode([]));

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"error":"The required option \"cartItemId\" is missing."}', $response->getContent());
    }
}
