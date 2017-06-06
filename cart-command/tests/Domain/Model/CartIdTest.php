<?php

declare(strict_types=1);

namespace Tests\Pamil\CartCommand\Domain\Model;

use Pamil\CartCommand\Domain\Model\CartId;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class CartIdTest extends TestCase
{
    /** @test */
    public function it_is_created_from_uuid_string(): void
    {
        $matchId = CartId::fromString('67c99578-798c-428a-a3fa-08ac9e20f8dd');

        Assert::assertInstanceOf(CartId::class, $matchId);
        Assert::assertSame('67c99578-798c-428a-a3fa-08ac9e20f8dd', $matchId->toString());
    }

    /** @test */
    public function it_is_generated(): void
    {
        $matchId = CartId::generate();

        Assert::assertInstanceOf(CartId::class, $matchId);
        Assert::assertRegExp('/' . Uuid::VALID_PATTERN . '/', $matchId->toString());
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function it_cannot_be_created_from_string_not_being_uuid(): void
    {
        CartId::fromString('Elon Musk');
    }
}
