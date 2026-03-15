<?php

namespace App\Modules\Competition\Configs;

use App\Modules\Competition\Contracts\CompetitionConfig;
use App\Modules\Competition\Contracts\HasSeasonGoals;
use App\Models\ClubProfile;
use App\Models\Game;

class PrimeraFedConfig implements CompetitionConfig, HasSeasonGoals
{
    private const TV_REVENUE = [
        1  => 200_000_000,  // €2M
        2  => 180_000_000,
        3  => 160_000_000,
        4  => 140_000_000,
        5  => 120_000_000,
        6  => 110_000_000,
        7  => 100_000_000,
        8  => 90_000_000,
        9  => 80_000_000,
        10 => 75_000_000,
        11 => 70_000_000,
        12 => 65_000_000,
        13 => 60_000_000,
        14 => 55_000_000,
    ];

    private const REPUTATION_TO_GOAL = [
        ClubProfile::REPUTATION_ELITE         => Game::GOAL_TITLE,
        ClubProfile::REPUTATION_CONTINENTAL   => Game::GOAL_PROMOTION,
        ClubProfile::REPUTATION_ESTABLISHED   => Game::GOAL_PROMOTION,
        ClubProfile::REPUTATION_MODEST        => Game::GOAL_TOP_HALF,
        ClubProfile::REPUTATION_LOCAL         => Game::GOAL_SURVIVAL,
    ];

    public function getTvRevenue(int $position): int
    {
        return self::TV_REVENUE[$position] ?? self::TV_REVENUE[14];
    }

    public function getPositionFactor(int $position): float
    {
        if ($position <= 3) return 1.10;
        if ($position <= 7) return 1.0;
        if ($position <= 10) return 0.95;
        return 0.85;
    }

    public function getSeasonGoal(string $reputation): string
    {
        return self::REPUTATION_TO_GOAL[$reputation] ?? Game::GOAL_TOP_HALF;
    }

    public function getGoalTargetPosition(string $goal): int
    {
        return match ($goal) {
            Game::GOAL_TITLE     => 1,
            Game::GOAL_PROMOTION => 2,
            Game::GOAL_TOP_HALF  => 7,
            Game::GOAL_SURVIVAL  => 12,
            default              => 7,
        };
    }

    public function getGoalLabel(string $goal): string
    {
        return match ($goal) {
            Game::GOAL_PROMOTION => 'game.goal_promotion',
            default              => 'game.goal_top_half',
        };
    }

    public function getStandingsZones(): array
    {
        return [
            ['min' => 1, 'max' => 2,  'class' => 'zone-promotion'],
            ['min' => 13,'max' => 14, 'class' => 'zone-relegation'],
        ];
    }

    public function getKnockoutPrizeMoney(int $roundNumber): int
    {
        return 0;
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
        return 400;
    }

    public function getRevenuePerSeat(): int
    {
        return 600;
    }
}
