<?php

declare(strict_types=1);

namespace App\Domain\Game;

use InvalidArgumentException;

final readonly class DrawOutcome
{
    public const MIN_NUMBER = 1;

    public const MAX_NUMBER = 1000;

    private function __construct(
        public int $number,
        public bool $isWin,
        public int $payoutCents,
    ) {}

    public static function forNumber(int $number): self
    {
        if ($number < self::MIN_NUMBER || $number > self::MAX_NUMBER) {
            throw new InvalidArgumentException(
                sprintf('Number must be between %d and %d, got %d.', self::MIN_NUMBER, self::MAX_NUMBER, $number)
            );
        }

        $isWin = $number % 2 === 0;

        return new self(
            number: $number,
            isWin: $isWin,
            payoutCents: $isWin ? $number * self::payoutPercent($number) : 0,
        );
    }

    private static function payoutPercent(int $number): int
    {
        return match (true) {
            $number > 900 => 70,
            $number > 600 => 50,
            $number > 300 => 30,
            default => 10,
        };
    }
}
