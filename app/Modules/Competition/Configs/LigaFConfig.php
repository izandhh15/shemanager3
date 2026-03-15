<?php

namespace App\Modules\Competition\Configs;

use App\Modules\Competition\Contracts\CompetitionConfig;
use App\Modules\Competition\Contracts\HasSeasonGoals;
use App\Models\ClubProfile;
use App\Models\Game;

class LigaFConfig implements CompetitionConfig, HasSeasonGoals
{
    /**
     * Liga F TV revenue by position (in cents).
     * Women's football revenues are significantly lower than men's.
     */
    private const TV_REVENUE = [
        1  => 1_500_000_000,  // €15M
        2  => 1_200_000_000,  // €12M
        3  => 900_000_000,    // €9M
        4  => 750_000_000,    // €7.5M
        5  => 650_000_000,    // €6.5M
        6  => 580_000_000,    // €5.8M
        7  => 520_000_000,    // €5.2M
        8  => 470_000_000,    // €4.7M
        9  => 430_000_000,    // €4.3M
        10 => 400_000_000,    // €4M
        11 => 370_000_000,    // €3.7M
        12 => 340_000_000,    // €3.4M
        13 => 320_000_000,    // €3.2M
        14 => 300_000_000,    // €3M
        15 => 280_000_000,    // €2.8M
        16 => 260_000_000,    // €2.6M
    ];

    private const POSITION_FACTORS = [
        'top'        => 1.10,
        'mid_high'   => 1.0,
        'mid_low'    => 0.95,
        'relegation' => 0.85,
    ];

    private const SEASON_GOALS = [
        Game::GOAL_TITLE        => ['targetPosition' => 1,  'label' => 'game.goal_title'],
        Game::GOAL_EUROPA_LEAGUE => ['targetPosition' => 4,  'label' => 'game.goal_europa_league'],
        Game::GOAL_TOP_HALF     => ['targetPosition' => 8,  'label' => 'game.goal_top_half'],
        Game::GOAL_SURVIVAL     => ['targetPosition' => 13, 'label' => 'game.goal_survival'],
    ];

    private const REPUTATION_TO_GOAL = [
        ClubProfile::REPUTATION_ELITE         => Game::GOAL_TITLE,
        ClubProfile::REPUTATION_CONTINENTAL   => Game::GOAL_EUROPA_LEAGUE,
        ClubProfile::REPUTATION_ESTABLISHED   => Game::GOAL_TOP_HALF,
        ClubProfile::REPUTATION_MODEST        => Game::GOAL_TOP_HALF,
        ClubProfile::REPUTATION_LOCAL         => Game::GOAL_SURVIVAL,
    ];

    public function getTvRevenue(int $position): int
    {
        return self::TV_REVENUE[$position] ?? self::TV_REVENUE[16];
    }

    public function getPositionFactor(int $position): float
    {
        if ($position <= 4) return self::POSITION_FACTORS['top'];
        if ($position <= 8) return self::POSITION_FACTORS['mid_high'];
        if ($position <= 12) return self::POSITION_FACTORS['mid_low'];
        return self::POSITION_FACTORS['relegation'];
    }

    public function getSeasonGoal(string $reputation): string
    {
        return self::REPUTATION_TO_GOAL[$reputation] ?? Game::GOAL_TOP_HALF;
    }

    public function getGoalTargetPosition(string $goal): int
    {
        return self::SEASON_GOALS[$goal]['targetPosition'] ?? 8;
    }

    public function getGoalLabel(string $goal): string
    {
        return self::SEASON_GOALS[$goal]['label'] ?? 'game.goal_top_half';
    }

    public function getStandingsZones(): array
    {
        return [
            ['min' => 1, 'max' => 1,  'class' => 'zone-champion'],
            ['min' => 2, 'max' => 4,  'class' => 'zone-ucl'],
            ['min' => 5, 'max' => 5,  'class' => 'zone-uwec'],
            ['min' => 13,'max' => 16, 'class' => 'zone-relegation'],
        ];
    }

    public function getKnockoutPrizeMoney(int $roundNumber): int
    {
        return 0; // Liga F has no knockout prize money
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
        return 800; // €8 per seat (lower than men's)
    }

    public function getRevenuePerSeat(): int
    {
        return 1200; // €12 per match per seat
    }
}
