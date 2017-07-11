<?php

declare(strict_types=1);

namespace Tests\Pamil\Behat\Context\Transform;

use Behat\Behat\Context\Context;

final class NumberContext implements Context
{
    /**
     * @var int[]
     */
    private static $wordsToNumbers = [
        'zero' => 0,
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
        'ten' => 10,
    ];

    /**
     * @Transform :number
     */
    public function transformQuantity(string $number): int
    {
        if (!array_key_exists($number, self::$wordsToNumbers)) {
            throw new \InvalidArgumentException(sprintf('Cannot transform "%s" to a number!', $number));
        }

        return self::$wordsToNumbers[$number];
    }
}
