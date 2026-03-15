<?php

namespace App\Modules\Competition\Configs;

use App\Modules\Competition\Contracts\CompetitionConfig;
use App\Modules\Competition\Contracts\HasSeasonGoals;
use App\Models\ClubProfile;
use App\Models\Game;

/**
 * Generic women's league config used for all foreign women's leagues
 * (Frauen-Bundesliga, Barclays WSL, Arkema Première Ligue, Serie A Women).
 * Revenue is lower than men's equivalents.
 */
class WomensLeagueConfig implements CompetitionConfig, HasSeasonGoals
{
    private int $numTeams;
    private int $baseTvRevenue;

    public function __construct(int $numTeams = 12, int $baseTvRevenue = 300_000_000)
    {
        $this->numTeams = $numTeams;
        $this->baseTvRevenue = $baseTvRevenue;
    }

    public function getTvRevenue(int $position): int
    {
        $positionRatio = 1 - (($position - 1) / max(1, $this->numTeams - 1));
        $multiplier = 0.7 + ($positionRatio * 1.3);
        return (int) ($this->baseTvRevenue * $multiplier);
    }

    public function getPositionFactor(int $position): float
    {
        $topQuarter = (int) ceil($this->numTeams * 0.25);
        $midPoint = (int) ceil($this->numTeams * 0.5);
        $bottomQuarter = (int) ceil($this->numTeams * 0.75);

        if ($position <= $topQuarter)  return 1.10;
        if ($position <= $midPoint)    return 1.0;
        if ($position <= $bottomQuarter) return 0.95;
        return 0.85;
    }

    public function getSeasonGoal(string $reputation): string
    {
        return match ($reputation) {
            ClubProfile::REPUTATION_ELITE       => Game::GOAL_TITLE,
            ClubProfile::REPUTATION_CONTINENTAL => Game::GOAL_EUROPA_LEAGUE,
            ClubProfile::REPUTATION_ESTABLISHED => Game::GOAL_TOP_HALF,
            ClubProfile::REPUTATION_MODEST      => Game::GOAL_TOP_HALF,
            default                             => Game::GOAL_SURVIVAL,
        };
    }

    public function getGoalTargetPosition(string $goal): int
    {
        return match ($goal) {
            Game::GOAL_TITLE         => 1,
            Game::GOAL_PROMOTION     => 2,
            Game::GOAL_EUROPA_LEAGUE => (int) ceil($this->numTeams * 0.25),
            Game::GOAL_TOP_HALF      => (int) ceil($this->numTeams * 0.5),
            Game::GOAL_SURVIVAL      => (int) ceil($this->numTeams * 0.85),
            default                  => (int) ceil($this->numTeams * 0.5),
        };
    }

    public function getGoalLabel(string $goal): string
    {
        return 'game.goal_top_half';
    }

    public function getStandingsZones(): array
    {
        return [
            ['min' => 1,                   'max' => 1,                   'class' => 'zone-champion'],
            ['min' => 2,                   'max' => (int) ceil($this->numTeams * 0.25), 'class' => 'zone-ucl'],
            ['min' => $this->numTeams - 1, 'max' => $this->numTeams,     'class' => 'zone-relegation'],
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
        return 600;
    }

    public function getRevenuePerSeat(): int
    {
        return 1000;
    }

    public function getKnockoutPrizeMoney(int $roundNumber): int
    {
        return 0;
    }
}
