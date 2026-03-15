<?php

namespace App\Http\Views;

use App\Modules\Competition\Services\CountryConfig;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Team;
use Illuminate\Http\Request;

final class SelectTeam
{
    public function __invoke(Request $request, CountryConfig $countryConfig)
    {
        if (Game::where('user_id', $request->user()->id)->count() >= 3) {
            return redirect()->route('dashboard')->withErrors(['limit' => __('messages.game_limit_reached')]);
        }

        // SheManager: use women's playable countries (ESF, DEF, ENF, FRF, ITF)
        // Fall back to all playable if women's codes not yet seeded
        $womenCodes = $countryConfig->womenPlayableCountryCodes();
        $codestoUse = !empty($womenCodes) ? $womenCodes : $countryConfig->playableCountryCodes();

        // Build country → tier → competition structure for career mode
        $countries = [];

        foreach ($codestoUse as $code) {
            $config = $countryConfig->get($code);
            $tiers = [];

            foreach ($config['tiers'] as $tier => $tierConfig) {
                $competition = Competition::with('teams')
                    ->find($tierConfig['competition']);

                if ($competition) {
                    $tiers[$tier] = $competition;
                }
            }

            if (!empty($tiers)) {
                $countries[$code] = [
                    'name'  => $config['name'],
                    'tiers' => $tiers,
                ];
            }
        }

        // Load Women's World Cup teams for tournament mode
        $wcTeams         = collect();
        $wcFeaturedTeams = collect();
        $hasTournamentMode = Competition::where('id', 'WWC2027')->exists();

        if ($hasTournamentMode) {
            $mappingPath = base_path('data/2026/WWC2027/team_mapping.json');

            if (file_exists($mappingPath)) {
                $teamMapping = json_decode(file_get_contents($mappingPath), true);

                // Collect team UUIDs from all confederations
                $allNations = [];
                foreach ($teamMapping as $key => $value) {
                    if (is_array($value) && isset($value['nations_ranked'])) {
                        foreach ($value['nations_ranked'] as $nation) {
                            if (!($nation['is_placeholder'] ?? false) && isset($nation['code'])) {
                                $allNations[] = $nation['code'];
                            }
                        }
                    }
                }

                // Match by country code (FIFA code stored as team country)
                $allWcTeams = Team::where('type', 'national')
                    ->whereIn('country', $allNations)
                    ->get()
                    ->sortBy('name')
                    ->values();

                // Featured national teams shown as larger cards
                $featuredCodes   = ['ESP', 'ENG', 'GER', 'FRA', 'USA', 'BRA', 'NED', 'SWE', 'NOR', 'JPN', 'AUS', 'COL'];
                $wcFeaturedTeams = $allWcTeams->filter(fn ($t) => in_array($t->country, $featuredCodes))->values();
                $wcTeams         = $allWcTeams->reject(fn ($t) => in_array($t->country, $featuredCodes))->values();
            }
        }

        return view('select-team', [
            'countries'        => $countries,
            'wcTeams'          => $wcTeams,
            'wcFeaturedTeams'  => $wcFeaturedTeams,
            'hasTournamentMode'=> $hasTournamentMode,
        ]);
    }
}
