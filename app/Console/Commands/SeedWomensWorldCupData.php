<?php

namespace App\Console\Commands;

use App\Modules\Player\Services\PlayerValuationService;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

/**
 * Seeds WWC2027 national team data from data/2026/WWC2027/teams/*.json
 * and the groups/bracket/schedule JSON files.
 *
 * Usage:
 *   php artisan app:seed-womens-world-cup --fresh
 */
class SeedWomensWorldCupData extends Command
{
    protected $signature = 'app:seed-womens-world-cup
                            {--fresh : Clear existing Women\'s World Cup data before seeding}';

    protected $description = 'Seed FIFA Women\'s World Cup 2027 national teams and players';

    private const COMPETITION_ID = 'WWC2027';
    private const SEASON         = '2026';
    private const DATA_PATH      = 'data/2026/WWC2027';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->clearExistingData();
        }

        $this->seedCompetition();
        $teamMapping = $this->seedTeams();
        $this->seedPlayers($teamMapping);
        $this->seedGroupsAndBracket($teamMapping);

        $this->displaySummary($teamMapping);

        return CommandAlias::SUCCESS;
    }

    private function clearExistingData(): void
    {
        $this->info('Clearing existing WWC2027 data...');

        $teamIds = DB::table('competition_teams')
            ->where('competition_id', self::COMPETITION_ID)
            ->pluck('team_id');

        DB::table('competition_teams')
            ->where('competition_id', self::COMPETITION_ID)
            ->delete();

        // Remove players linked to these national teams only
        foreach ($teamIds as $teamId) {
            DB::table('players')
                ->where('team_id', $teamId)
                ->delete();
        }

        DB::table('competitions')
            ->where('id', self::COMPETITION_ID)
            ->delete();

        $this->info('Cleared.');
    }

    private function seedCompetition(): void
    {
        DB::table('competitions')->updateOrInsert(
            ['id' => self::COMPETITION_ID],
            [
                'name'         => 'FIFA Women\'s World Cup 2027',
                'country'      => 'WCF',
                'flag'         => 'un',
                'tier'         => 1,
                'type'         => 'cup',
                'role'         => 'tournament',
                'scope'        => 'continental',
                'handler_type' => 'group_stage_cup',
                'season'       => self::SEASON,
            ]
        );

        $this->info('Competition record: FIFA Women\'s World Cup 2027');
    }

    /**
     * Seed all national teams from teams/*.json files.
     *
     * @return array<string, string>  fifa_code → team_uuid
     */
    private function seedTeams(): array
    {
        $teamMapping = [];
        $teamMappingJson = $this->loadJson(self::DATA_PATH . '/team_mapping.json');
        $basePath = base_path(self::DATA_PATH . '/teams');

        if (!is_dir($basePath)) {
            $this->error("Teams directory not found: {$basePath}");
            return $teamMapping;
        }

        $count = 0;

        foreach (glob("{$basePath}/*.json") as $filePath) {
            $fifaCode = strtoupper(basename($filePath, '.json'));
            $data     = $this->loadJson($filePath);
            $teamName = $data['name'] ?? $fifaCode;

            // Skip empty placeholder files
            if (empty($teamName) || $teamName === $fifaCode) {
                // Try to get name from team_mapping.json
                foreach ($teamMappingJson as $confederation => $confData) {
                    if (is_array($confData) && isset($confData['nations_ranked'])) {
                        foreach ($confData['nations_ranked'] as $nation) {
                            if (($nation['code'] ?? '') === $fifaCode) {
                                $teamName = $nation['name'];
                                break 2;
                            }
                        }
                    }
                }
            }

            $existing = DB::table('teams')
                ->where('country', $fifaCode)
                ->where('type', 'national')
                ->first();

            if ($existing) {
                $teamId = $existing->id;
            } else {
                $teamId = Str::uuid()->toString();
                DB::table('teams')->insert([
                    'id'    => $teamId,
                    'name'  => $teamName,
                    'country' => $fifaCode,
                    'type'  => 'national',
                    'image' => $data['image'] ?? null,
                    'stadium_name'  => null,
                    'stadium_seats' => 0,
                ]);
            }

            $teamMapping[$fifaCode] = $teamId;

            // Link to competition
            DB::table('competition_teams')->updateOrInsert(
                [
                    'competition_id' => self::COMPETITION_ID,
                    'team_id'        => $teamId,
                    'season'         => self::SEASON,
                ],
                ['entry_round' => 1]
            );

            $count++;
        }

        $this->info("National teams seeded: {$count}");

        return $teamMapping;
    }

    /**
     * Seed players from national team JSON files.
     *
     * @param array<string, string> $teamMapping fifaCode → teamId
     */
    private function seedPlayers(array $teamMapping): void
    {
        $valuationService = app(PlayerValuationService::class);
        $basePath         = base_path(self::DATA_PATH . '/teams');
        $total            = 0;

        foreach (glob("{$basePath}/*.json") as $filePath) {
            $fifaCode = strtoupper(basename($filePath, '.json'));
            $teamId   = $teamMapping[$fifaCode] ?? null;

            if (!$teamId) {
                continue;
            }

            $data    = $this->loadJson($filePath);
            $players = $data['players'] ?? [];

            if (empty($players)) {
                continue;
            }

            foreach ($players as $player) {
                $playerId = $player['id'] ?? null;
                if (!$playerId) {
                    continue;
                }

                $dateOfBirth = null;
                $age         = null;
                if (!empty($player['dateOfBirth'])) {
                    try {
                        $dob         = Carbon::parse($player['dateOfBirth']);
                        $dateOfBirth = $dob->toDateString();
                        $age         = $dob->age;
                    } catch (\Exception) {
                    }
                }

                $foot = match (strtolower($player['foot'] ?? '')) {
                    'left'  => 'left',
                    'right' => 'right',
                    'both'  => 'both',
                    default => null,
                };

                $marketValueCents = Money::parseMarketValue($player['marketValue'] ?? null);
                $position         = $player['position'] ?? 'Central Midfield';
                [$technical, $physical] = $valuationService->marketValueToAbilities(
                    $marketValueCents,
                    $position,
                    $age ?? 25
                );

                $values = [
                    'name'             => $player['name'],
                    'date_of_birth'    => $dateOfBirth,
                    'nationality'      => json_encode($player['nationality'] ?? []),
                    'height'           => $player['height'] ?? null,
                    'foot'             => $foot,
                    'technical_ability'=> $technical,
                    'physical_ability' => $physical,
                ];

                $exists = DB::table('players')
                    ->where('transfermarkt_id', $playerId)
                    ->exists();

                if ($exists) {
                    DB::table('players')
                        ->where('transfermarkt_id', $playerId)
                        ->update($values);
                } else {
                    DB::table('players')->insert(array_merge(
                        ['id' => Str::uuid()->toString(), 'transfermarkt_id' => $playerId],
                        $values
                    ));
                }

                $total++;
            }
        }

        $this->info("Players seeded: {$total}");
    }

    /**
     * Validate groups.json exists and log group composition for debugging.
     *
     * @param array<string, string> $teamMapping fifaCode → teamId
     */
    private function seedGroupsAndBracket(array $teamMapping): void
    {
        $groupsPath = base_path(self::DATA_PATH . '/groups.json');

        if (!file_exists($groupsPath)) {
            $this->warn('groups.json not found.');
            return;
        }

        $groups = $this->loadJson($groupsPath);
        $groupCount = 0;
        $teamCount  = 0;

        foreach ($groups as $groupLetter => $groupData) {
            if (!is_array($groupData) || !isset($groupData['teams'])) {
                continue;
            }

            $groupCount++;
            foreach ($groupData['teams'] as $fifaCode) {
                if (isset($teamMapping[$fifaCode])) {
                    $teamCount++;
                }
            }

            $teamList = implode(', ', $groupData['teams']);
            $this->line("  Group {$groupLetter}: {$teamList}");
        }

        $this->info("Groups validated: {$groupCount} groups, {$teamCount} team assignments");
        $this->line('  Note: Group fixtures are generated per-game at game creation time.');
    }

    private function displaySummary(array $teamMapping): void
    {
        $this->newLine();
        $this->info('=== WWC2027 Seed Summary ===');
        $this->line('  Teams: '   . count($teamMapping));
        $this->line('  Players: ' . DB::table('players')->where('transfermarkt_id', 'like', 'wnt_%')->count());
        $this->newLine();
        $this->info('Done! Run php artisan app:seed-reference-data --season=2026 to seed club competitions.');
    }

    private function loadJson(string $path): array
    {
        $fullPath = str_starts_with($path, '/') ? $path : base_path($path);

        if (!file_exists($fullPath)) {
            $this->warn("JSON file not found: {$fullPath}");
            return [];
        }

        $content = file_get_contents($fullPath);
        $data    = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->warn("Invalid JSON in {$fullPath}: " . json_last_error_msg());
            return [];
        }

        return $data;
    }
}
