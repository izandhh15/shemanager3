<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Football Country Configurations
    |--------------------------------------------------------------------------
    |
    | Each country declares its full football ecosystem: playable league tiers,
    | domestic cups, promotion/relegation rules, continental qualification slots,
    | and support teams needed for transfers and continental competitions.
    |
    | This config is the single source of truth for country-specific setup.
    | Processors, seeders, and game creation all read from here.
    |
    */

    'ES' => [
        'name' => 'España',

        'tiers' => [
            1 => [
                'competition' => 'ESP1',
                'teams' => 20,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\LaLigaConfig::class,
            ],
            2 => [
                'competition' => 'ESP2',
                'teams' => 22,
                'handler' => 'league_with_playoff',
                'config_class' => \App\Modules\Competition\Configs\LaLiga2Config::class,
            ],
        ],

        'domestic_cups' => [
            'ESPCUP' => [
                'handler' => 'knockout_cup',
                'config_class' => \App\Modules\Competition\Configs\KnockoutCupConfig::class,
            ],
            'ESPSUP' => [
                'handler' => 'knockout_cup',
            ],
        ],

        'supercup' => [
            'competition' => 'ESPSUP',
            'cup' => 'ESPCUP',
            'league' => 'ESP1',
            'cup_final_round' => 7,
            'cup_entry_round' => 3,
        ],

        'promotions' => [
            [
                'top_division' => 'ESP1',
                'bottom_division' => 'ESP2',
                'relegated_positions' => [18, 19, 20],
                'direct_promotion_positions' => [1, 2],
                'playoff_positions' => [3, 4, 5, 6],
                'playoff_generator' => \App\Modules\Competition\Playoffs\ESP2PlayoffGenerator::class,
            ],
        ],

        // Reserve teams that cannot be promoted to the same division as their parent.
        // Maps child transfermarkt_id => parent transfermarkt_id.
        'reserve_teams' => [
            9899 => 681,   // Real Sociedad B → Real Sociedad
        ],

        'continental_slots' => [
            'ESP1' => [
                'UCL' => [1, 2, 3, 4, 5],
                'UEL' => [6],
                'UECL' => [7],
            ],
        ],

        'cup_winner_slot' => [
            'cup' => 'ESPCUP',
            'competition' => 'UEL',
            'league' => 'ESP1',
        ],

        'continental_competitions' => [
            'UCL' => [
                'config_class' => \App\Modules\Competition\Configs\ChampionsLeagueConfig::class,
            ],
            'UEL' => [
                'config_class' => \App\Modules\Competition\Configs\EuropaLeagueConfig::class,
            ],
            'UECL' => [
                'config_class' => \App\Modules\Competition\Configs\ConferenceLeagueConfig::class,
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Support teams: non-playable teams needed for competition and transfers
        |----------------------------------------------------------------------
        |
        | Categories (initialized in this order during game setup):
        |   1. transfer_pool — foreign league teams for scouting/transfers/loans
        |   2. continental   — opponents in UEFA competitions (reuse pool rosters)
        |
        | Domestic cup teams (ESPCUP lower-division) are linked at seeding time
        | but don't need GamePlayer rosters — early rounds are auto-simulated.
        */
        'support' => [
            'transfer_pool' => [
                // Other top-flight leagues — full rosters from JSON, eagerly loaded at game setup
                'ENG1' => ['role' => 'league', 'handler' => 'league', 'country' => 'EN'],
                'DEU1' => ['role' => 'league', 'handler' => 'league', 'country' => 'DE'],
                'FRA1' => ['role' => 'league', 'handler' => 'league', 'country' => 'FR'],
                'ITA1' => ['role' => 'league', 'handler' => 'league', 'country' => 'IT'],
                // EUR club pool — individual team files, includes NLD/POR teams
                'EUR'  => ['role' => 'team_pool', 'handler' => 'team_pool', 'country' => 'EU'],
            ],
            'continental' => [
                // Teams needed for European competitions — rosters reused from
                // tiers + transfer_pool where possible, gaps filled from EUR pool
                'UCL' => ['handler' => 'swiss_format', 'country' => 'EU'],
                'UEL' => ['handler' => 'swiss_format', 'country' => 'EU'],
            ],
        ],
    ],

    'EN' => [
        'name' => 'England',

        'tiers' => [
            1 => [
                'competition' => 'ENG1',
                'teams' => 20,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\PremierLeagueConfig::class,
            ],
        ],

        'domestic_cups' => [],
        'promotions' => [],

        'continental_slots' => [
            'ENG1' => [
                'UCL' => [1, 2, 3, 4, 5],
                'UEL' => [6],
                'UECL' => [7],
            ],
        ],

        'cup_winner_slot' => null,

        'continental_competitions' => [
            'UCL' => [
                'config_class' => \App\Modules\Competition\Configs\ChampionsLeagueConfig::class,
            ],
            'UEL' => [
                'config_class' => \App\Modules\Competition\Configs\EuropaLeagueConfig::class,
            ],
            'UECL' => [
                'config_class' => \App\Modules\Competition\Configs\ConferenceLeagueConfig::class,
            ],
        ],

        'support' => [
            'transfer_pool' => [
                'ESP1' => ['role' => 'league', 'handler' => 'league', 'country' => 'ES'],
                'DEU1' => ['role' => 'league', 'handler' => 'league', 'country' => 'DE'],
                'FRA1' => ['role' => 'league', 'handler' => 'league', 'country' => 'FR'],
                'ITA1' => ['role' => 'league', 'handler' => 'league', 'country' => 'IT'],
                'EUR'  => ['role' => 'team_pool', 'handler' => 'team_pool', 'country' => 'EU'],
            ],
            'continental' => [
                'UCL' => ['handler' => 'swiss_format', 'country' => 'EU'],
                'UEL' => ['handler' => 'swiss_format', 'country' => 'EU'],
            ],
        ],
    ],

    'DE' => [
        'name' => 'Deutschland',

        'tiers' => [
            1 => [
                'competition' => 'DEU1',
                'teams' => 18,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\BundesligaConfig::class,
            ],
        ],

        'domestic_cups' => [],
        'promotions' => [],

        'continental_slots' => [
            'DEU1' => [
                'UCL' => [1, 2, 3, 4],
                'UEL' => [5, 6],
                'UECL' => [7],
            ],
        ],

        'cup_winner_slot' => null,

        'continental_competitions' => [
            'UCL' => [
                'config_class' => \App\Modules\Competition\Configs\ChampionsLeagueConfig::class,
            ],
            'UEL' => [
                'config_class' => \App\Modules\Competition\Configs\EuropaLeagueConfig::class,
            ],
            'UECL' => [
                'config_class' => \App\Modules\Competition\Configs\ConferenceLeagueConfig::class,
            ],
        ],

        'support' => [
            'transfer_pool' => [
                'ESP1' => ['role' => 'league', 'handler' => 'league', 'country' => 'ES'],
                'ENG1' => ['role' => 'league', 'handler' => 'league', 'country' => 'EN'],
                'FRA1' => ['role' => 'league', 'handler' => 'league', 'country' => 'FR'],
                'ITA1' => ['role' => 'league', 'handler' => 'league', 'country' => 'IT'],
                'EUR'  => ['role' => 'team_pool', 'handler' => 'team_pool', 'country' => 'EU'],
            ],
            'continental' => [
                'UCL' => ['handler' => 'swiss_format', 'country' => 'EU'],
                'UEL' => ['handler' => 'swiss_format', 'country' => 'EU'],
            ],
        ],
    ],

    'IT' => [
        'name' => 'Italia',

        'tiers' => [
            1 => [
                'competition' => 'ITA1',
                'teams' => 20,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\SerieAConfig::class,
            ],
        ],

        'domestic_cups' => [],
        'promotions' => [],

        'continental_slots' => [
            'ITA1' => [
                'UCL' => [1, 2, 3, 4, 5],
                'UEL' => [6],
                'UECL' => [7],
            ],
        ],

        'cup_winner_slot' => null,

        'continental_competitions' => [
            'UCL' => [
                'config_class' => \App\Modules\Competition\Configs\ChampionsLeagueConfig::class,
            ],
            'UEL' => [
                'config_class' => \App\Modules\Competition\Configs\EuropaLeagueConfig::class,
            ],
            'UECL' => [
                'config_class' => \App\Modules\Competition\Configs\ConferenceLeagueConfig::class,
            ],
        ],

        'support' => [
            'transfer_pool' => [
                'ESP1' => ['role' => 'league', 'handler' => 'league', 'country' => 'ES'],
                'ENG1' => ['role' => 'league', 'handler' => 'league', 'country' => 'EN'],
                'DEU1' => ['role' => 'league', 'handler' => 'league', 'country' => 'DE'],
                'FRA1' => ['role' => 'league', 'handler' => 'league', 'country' => 'FR'],
                'EUR'  => ['role' => 'team_pool', 'handler' => 'team_pool', 'country' => 'EU'],
            ],
            'continental' => [
                'UCL' => ['handler' => 'swiss_format', 'country' => 'EU'],
                'UEL' => ['handler' => 'swiss_format', 'country' => 'EU'],
            ],
        ],
    ],

    'FR' => [
        'name' => 'France',

        'tiers' => [
            1 => [
                'competition' => 'FRA1',
                'teams' => 18,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\Ligue1Config::class,
            ],
        ],

        'domestic_cups' => [],
        'promotions' => [],

        'continental_slots' => [
            'FRA1' => [
                'UCL' => [1, 2, 3],
                'UEL' => [4],
                'UECL' => [5],
            ],
        ],

        'cup_winner_slot' => null,

        'continental_competitions' => [
            'UCL' => [
                'config_class' => \App\Modules\Competition\Configs\ChampionsLeagueConfig::class,
            ],
            'UEL' => [
                'config_class' => \App\Modules\Competition\Configs\EuropaLeagueConfig::class,
            ],
            'UECL' => [
                'config_class' => \App\Modules\Competition\Configs\ConferenceLeagueConfig::class,
            ],
        ],

        'support' => [
            'transfer_pool' => [
                'ESP1' => ['role' => 'league', 'handler' => 'league', 'country' => 'ES'],
                'ENG1' => ['role' => 'league', 'handler' => 'league', 'country' => 'EN'],
                'DEU1' => ['role' => 'league', 'handler' => 'league', 'country' => 'DE'],
                'ITA1' => ['role' => 'league', 'handler' => 'league', 'country' => 'IT'],
                'EUR'  => ['role' => 'team_pool', 'handler' => 'team_pool', 'country' => 'EU'],
            ],
            'continental' => [
                'UCL' => ['handler' => 'swiss_format', 'country' => 'EU'],
                'UEL' => ['handler' => 'swiss_format', 'country' => 'EU'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | España Femenino (ESF) — Women's Football
    |--------------------------------------------------------------------------
    |
    | Tier 1: Liga F (16 teams)
    | Tier 2: Primera Federación Iberdrola (14 teams)
    | Domestic cups: Copa de la Reina, Supercopa de España Femenina
    | Continental: UEFA Women's Champions League, UEFA Women's Europa Cup
    |
    */

    'ESF' => [
        'name' => 'España Femenino',

        'tiers' => [
            1 => [
                'competition' => 'LIGAF',
                'teams' => 16,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\LigaFConfig::class,
            ],
            2 => [
                'competition' => 'PRIMFED',
                'teams' => 14,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\PrimeraFedConfig::class,
            ],
        ],

        'domestic_cups' => [
            'COPREINA' => [
                'handler' => 'knockout_cup',
                'config_class' => \App\Modules\Competition\Configs\KnockoutCupConfig::class,
            ],
            'SUPESPF' => [
                'handler' => 'knockout_cup',
            ],
        ],

        'supercup' => [
            'competition' => 'SUPESPF',
            'cup' => 'COPREINA',
            'league' => 'LIGAF',
            'cup_final_round' => 6,
            'cup_entry_round' => 3,
        ],

        'promotions' => [
            [
                'top_division'              => 'LIGAF',
                'bottom_division'           => 'PRIMFED',
                'relegated_positions'       => [14, 15, 16],
                'direct_promotion_positions'=> [1, 2],
                'playoff_positions'         => [3, 4, 5, 6],
            ],
        ],

        'reserve_teams' => [],

        'continental_slots' => [
            'LIGAF' => [
                'UWCL' => [1, 2, 3, 4],
                'UWEC' => [5, 6],
            ],
        ],

        'cup_winner_slot' => [
            'cup'         => 'COPREINA',
            'competition' => 'UWCL',
            'league'      => 'LIGAF',
        ],

        'continental_competitions' => [
            'UWCL' => [
                'config_class' => \App\Modules\Competition\Configs\WomensChampionsLeagueConfig::class,
            ],
            'UWEC' => [
                'config_class' => \App\Modules\Competition\Configs\KnockoutCupConfig::class,
            ],
        ],

        'support' => [
            'transfer_pool' => [
                'BUNDESF' => ['role' => 'league', 'handler' => 'league', 'country' => 'DEF'],
                'BWSL'    => ['role' => 'league', 'handler' => 'league', 'country' => 'ENF'],
                'APLF'    => ['role' => 'league', 'handler' => 'league', 'country' => 'FRF'],
                'SERIAF'  => ['role' => 'league', 'handler' => 'league', 'country' => 'ITF'],
            ],
            'continental' => [
                'UWCL' => ['handler' => 'swiss_format', 'country' => 'EUF'],
                'UWEC' => ['handler' => 'knockout_cup', 'country' => 'EUF'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Foreign Women's Leagues (support only — not playable)
    |--------------------------------------------------------------------------
    */

    'DEF' => [
        'name' => 'Deutschland Frauen',
        'tiers' => [
            1 => [
                'competition' => 'BUNDESF',
                'teams' => 14,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\WomensLeagueConfig::class,
            ],
        ],
        'domestic_cups' => [],
        'promotions' => [],
        'continental_slots' => [
            'BUNDESF' => [
                'UWCL' => [1, 2],
                'UWEC' => [3, 4],
            ],
        ],
        'cup_winner_slot' => null,
        'continental_competitions' => [],
        'support' => [
            'transfer_pool' => [
                'LIGAF'  => ['role' => 'league', 'handler' => 'league', 'country' => 'ESF'],
                'BWSL'   => ['role' => 'league', 'handler' => 'league', 'country' => 'ENF'],
                'APLF'   => ['role' => 'league', 'handler' => 'league', 'country' => 'FRF'],
                'SERIAF' => ['role' => 'league', 'handler' => 'league', 'country' => 'ITF'],
            ],
            'continental' => [
                'UWCL' => ['handler' => 'swiss_format', 'country' => 'EUF'],
            ],
        ],
    ],

    'ENF' => [
        'name' => 'England Women',
        'tiers' => [
            1 => [
                'competition' => 'BWSL',
                'teams' => 12,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\WomensLeagueConfig::class,
            ],
        ],
        'domestic_cups' => [],
        'promotions' => [],
        'continental_slots' => [
            'BWSL' => [
                'UWCL' => [1, 2],
                'UWEC' => [3, 4],
            ],
        ],
        'cup_winner_slot' => null,
        'continental_competitions' => [],
        'support' => [
            'transfer_pool' => [
                'LIGAF'   => ['role' => 'league', 'handler' => 'league', 'country' => 'ESF'],
                'BUNDESF' => ['role' => 'league', 'handler' => 'league', 'country' => 'DEF'],
                'APLF'    => ['role' => 'league', 'handler' => 'league', 'country' => 'FRF'],
                'SERIAF'  => ['role' => 'league', 'handler' => 'league', 'country' => 'ITF'],
            ],
            'continental' => [
                'UWCL' => ['handler' => 'swiss_format', 'country' => 'EUF'],
            ],
        ],
    ],

    'FRF' => [
        'name' => 'France Féminin',
        'tiers' => [
            1 => [
                'competition' => 'APLF',
                'teams' => 12,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\WomensLeagueConfig::class,
            ],
        ],
        'domestic_cups' => [],
        'promotions' => [],
        'continental_slots' => [
            'APLF' => [
                'UWCL' => [1, 2],
                'UWEC' => [3],
            ],
        ],
        'cup_winner_slot' => null,
        'continental_competitions' => [],
        'support' => [
            'transfer_pool' => [
                'LIGAF'   => ['role' => 'league', 'handler' => 'league', 'country' => 'ESF'],
                'BUNDESF' => ['role' => 'league', 'handler' => 'league', 'country' => 'DEF'],
                'BWSL'    => ['role' => 'league', 'handler' => 'league', 'country' => 'ENF'],
                'SERIAF'  => ['role' => 'league', 'handler' => 'league', 'country' => 'ITF'],
            ],
            'continental' => [
                'UWCL' => ['handler' => 'swiss_format', 'country' => 'EUF'],
            ],
        ],
    ],

    'ITF' => [
        'name' => 'Italia Femminile',
        'tiers' => [
            1 => [
                'competition' => 'SERIAF',
                'teams' => 12,
                'handler' => 'league',
                'config_class' => \App\Modules\Competition\Configs\WomensLeagueConfig::class,
            ],
        ],
        'domestic_cups' => [],
        'promotions' => [],
        'continental_slots' => [
            'SERIAF' => [
                'UWCL' => [1, 2],
                'UWEC' => [3],
            ],
        ],
        'cup_winner_slot' => null,
        'continental_competitions' => [],
        'support' => [
            'transfer_pool' => [
                'LIGAF'   => ['role' => 'league', 'handler' => 'league', 'country' => 'ESF'],
                'BUNDESF' => ['role' => 'league', 'handler' => 'league', 'country' => 'DEF'],
                'BWSL'    => ['role' => 'league', 'handler' => 'league', 'country' => 'ENF'],
                'APLF'    => ['role' => 'league', 'handler' => 'league', 'country' => 'FRF'],
            ],
            'continental' => [
                'UWCL' => ['handler' => 'swiss_format', 'country' => 'EUF'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | FIFA Women's World Cup 2027 (Tournament Mode)
    |--------------------------------------------------------------------------
    */

    'WCF' => [
        'name' => 'Copa del Mundo Femenina',
        'tournament' => true,

        'tiers' => [
            1 => [
                'competition' => 'WWC2027',
                'teams' => 32,
                'handler' => 'group_stage_cup',
            ],
        ],

        'domestic_cups' => [],
        'promotions' => [],
        'continental_slots' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | World Cup (Tournament Mode)
    |--------------------------------------------------------------------------
    |
    | The World Cup uses the main teams/players tables with type='national'.
    | Game creation in tournament mode reads from these teams and the WC2026
    | competition via competition_teams, sharing Player records with career mode.
    |
    */

    'WC' => [
        'name' => 'Copa del Mundo',
        'tournament' => true,

        'tiers' => [
            1 => [
                'competition' => 'WC2026',
                'teams' => 48,
                'handler' => 'world_cup',
            ],
        ],

        'domestic_cups' => [],
        'promotions' => [],
        'continental_slots' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Test Country (used by test suite)
    |--------------------------------------------------------------------------
    */

    'XX' => [
        'name' => 'Test Country',

        'tiers' => [
            1 => [
                'competition' => 'TEST1',
                'teams' => 4,
                'handler' => 'league',
            ],
        ],

        'domestic_cups' => [
            'TESTCUP' => [
                'handler' => 'knockout_cup',
            ],
        ],

        'promotions' => [],
        'continental_slots' => [],
    ],

];
