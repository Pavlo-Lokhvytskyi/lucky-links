<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Game\DrawOutcome;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DrawOutcomeTest extends TestCase
{
    #[DataProvider('numbers')]
    public function test_it_resolves_result_and_payout(int $number, bool $isWin, int $payoutCents): void
    {
        $outcome = DrawOutcome::forNumber($number);

        $this->assertSame($number, $outcome->number);
        $this->assertSame($isWin, $outcome->isWin);
        $this->assertSame($payoutCents, $outcome->payoutCents);
    }

    /** @return array<string, array{int, bool, int}> */
    public static function numbers(): array
    {
        return [
            // Odd numbers lose and pay nothing, however big they are.
            'odd, highest tier' => [999, false, 0],
            'odd, lowest tier' => [1, false, 0],

            // Even numbers win a share of the number itself, by tier.
            '1000 -> 70%' => [1000, true, 70_000],
            '902 -> 70%' => [902, true, 63_140],
            '900 -> 50%, boundary is exclusive' => [900, true, 45_000],
            '602 -> 50%' => [602, true, 30_100],
            '600 -> 30%, boundary is exclusive' => [600, true, 18_000],
            '302 -> 30%' => [302, true, 9_060],
            '300 -> 10%, boundary is inclusive' => [300, true, 3_000],
            '2 -> 10%' => [2, true, 20],
        ];
    }

    #[DataProvider('numbersOutOfRange')]
    public function test_it_rejects_numbers_out_of_range(int $number): void
    {
        $this->expectException(InvalidArgumentException::class);

        DrawOutcome::forNumber($number);
    }

    /** @return array<string, array{int}> */
    public static function numbersOutOfRange(): array
    {
        return [
            'below range' => [0],
            'above range' => [1001],
        ];
    }
}
