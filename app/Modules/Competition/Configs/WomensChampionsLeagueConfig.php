<?php

namespace App\Modules\Competition\Configs;

use App\Modules\Competition\Contracts\CompetitionConfig;

class WomensChampionsLeagueConfig implements CompetitionConfig
{
    /**
     * UWCL prize money by round (in cents).
     */
    private const ROUND_PRIZE_MONEY = [
        1 => 50_000_000,    // €500K - League phase participation
        2 => 80_000_000,    // €800K - League phase win
        3 => 200_000_000,   // €2M - Quarter-finals
        4 => 400_000_000,   // €4M - Semi-finals
        5 => 600_000_000,   // €6M - Final (runner-up)
        6 => 1_000_000_000, // €10M - Winner
    ];

    public function getTvRevenue(int $position): int
    {
        return 0;
    }

    public function getPositionFactor(int $position): float
    {
        return 1.0;
    }

    public function getKnockoutPrizeMoney(int $roundNumber): int
    {
        return self::ROUND_PRIZE_MONEY[$roundNumber] ?? self::ROUND_PRIZE_MONEY[1];
    }

    public function getStandingsZones(): array
    {
        return [
            ['min' => 1, 'max' => 2,  'class' => 'zone-champion'],
            ['min' => 3, 'max' => 6,  'class' => 'zone-promotion'],
            ['min' => 7, 'max' => 9,  'class' => 'zone-relegation'],
        ];
    }

    public function getTopScorerAwardName(): string
    {
        return 'season.top_scorer';
    }

    public function getBestGoalkeeperAwardName(): string
    {
        return 'season.best_goalkeeper';
    }

    public function getCommercialPerSeat(): int
    {
        return 1500;
    }

    public function getRevenuePerSeat(): int
    {
        return 2500;
    }
}
