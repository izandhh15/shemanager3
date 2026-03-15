# SheManager — Fútbol Femenino 2025-26

Manager de fútbol femenino desarrollado por **Izan Delgado** ([@izandhh](https://x.com/izandhh)), basado en el código open source de [Virtua FC](https://github.com/pabloroman/virtua-fc) de Pablo Román. Compatible con el codebase Laravel original, añade soporte completo para la temporada 2025-26 femenina.

---

## Instalación

```bash
# 1. Instalar dependencias
composer install
npm install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Correr migraciones
php artisan migrate

# 4. Sembrar datos del fútbol FEMENINO (temporada 2026)
php artisan app:seed-reference-data --season=2026 --fresh

# 5. (Opcional) Sembrar también datos masculinos (temporada 2025)
php artisan app:seed-reference-data --season=2025

# 6. Arrancar el servidor de desarrollo
composer dev
```

---

## Competiciones disponibles

### 🇪🇸 España Femenino (código país: `ESF`)

| Código | Nombre | Equipos | Formato |
|--------|--------|---------|---------|
| `LIGAF` | Liga F | 16 | Liga 30 jornadas |
| `PRIMFED` | Primera Federación Iberdrola | 14 | Liga 30 jornadas |
| `COPREINA` | Copa de la Reina | 48 equipos (3 divisiones) | Eliminatoria 6 rondas |
| `SUPESPF` | Supercopa de España Femenina | 4 | Final Four |
| `UWCL` | UEFA Women's Champions League | 16 (fase liga) | Liga + Eliminatoria |
| `UWEC` | UEFA Women's Europa Cup | 63 | Eliminatoria |

### 🌍 Ligas extranjeras (pool de transferencias)

| Código | Nombre | País | Equipos |
|--------|--------|------|---------|
| `BUNDESF` | Google Pixel Frauen-Bundesliga | 🇩🇪 Alemania | 14 |
| `BWSL` | Barclays Women's Super League | 🏴󠁧󠁢󠁥󠁮󠁧󠁿 Inglaterra | 12 |
| `APLF` | Arkema Première Ligue | 🇫🇷 Francia | 12 |
| `SERIAF` | Serie A Women Athora | 🇮🇹 Italia | 12 |

### 🌐 Copa del Mundo Femenina

| Código | Nombre | Selecciones |
|--------|--------|-------------|
| `WWC2027` | FIFA Women's World Cup 2027 | 32 (176 clasificatorias) |

---

## Seeder de selecciones nacionales (WWC2027)

```bash
# Sembrar selecciones nacionales del Mundial Femenino 2027
php artisan app:seed-womens-world-cup

# Con limpieza previa
php artisan app:seed-womens-world-cup --fresh
```

---

## Compatibilidad con modo masculino

Los dos modos (masculino `--season=2025` y femenino `--season=2026`) coexisten en la misma base de datos. Los IDs de competición son distintos (`ESP1` vs `LIGAF`, `UCL` vs `UWCL`, etc.), por lo que no hay colisiones.

---

## Diferencias con el modo masculino

| Aspecto | Masculino | Femenino |
|---------|-----------|----------|
| Liga principal | `ESP1` La Liga (20) | `LIGAF` Liga F (16) |
| Segunda división | `ESP2` LaLiga2 (22) | `PRIMFED` Primera Fed. (14) |
| Copa nacional | `ESPCUP` Copa del Rey | `COPREINA` Copa de la Reina |
| Supercopa | `ESPSUP` | `SUPESPF` |
| Champions | `UCL` (36 equipos) | `UWCL` (9 fase liga + clasificatorias) |
| Europa | `UEL` | `UWEC` |
| Revenue base | ~€155M 1.º LaLiga | ~€15M 1.º Liga F |
| Directorio datos | `data/2025/` | `data/2026/` |
| Código país | `ES` | `ESF` |

---

## Estructura de archivos añadidos

```
app/
├── Console/Commands/
│   └── SeedWomensWorldCupData.php      # Nuevo: siembra WWC2027
├── Modules/Competition/Configs/
│   ├── LigaFConfig.php                  # Nuevo: config financiera Liga F
│   ├── PrimeraFedConfig.php             # Nuevo: config Primera Federación
│   ├── WomensChampionsLeagueConfig.php  # Nuevo: config UWCL
│   └── WomensLeagueConfig.php           # Nuevo: config ligas extranjeras
└── Modules/Competition/Services/
    └── CountryConfig.php                # Modificado: +womenPlayableCountryCodes()

config/
└── countries.php                        # Modificado: +ESF, DEF, ENF, FRF, ITF, WCF

data/
└── 2026/                               # Nuevo: datos temporada femenina
    ├── LIGAF/                          # Liga F (equipos + calendario)
    ├── PRIMFED/                        # Primera Federación Iberdrola
    ├── COPREINA/                       # Copa de la Reina (bracket completo)
    ├── SUPESPF/                        # Supercopa de España Femenina
    ├── UWCL/                           # UWCL (fase liga + rondas previas)
    ├── UWEC/                           # UEFA Women's Europa Cup
    ├── BUNDESF/                        # Frauen-Bundesliga
    ├── BWSL/                           # Women's Super League
    ├── APLF/                           # Arkema Première Ligue
    ├── SERIAF/                         # Serie A Women
    └── WWC2027/                        # Mundial Femenino 2027
        ├── groups.json
        ├── bracket.json
        ├── schedule.json
        ├── qualifying.json
        ├── team_mapping.json           # 176 selecciones con ranking FIFA
        └── teams/                      # 22 plantillas completas + 154 placeholders
            ├── ESP.json  ENG.json  GER.json  FRA.json
            ├── USA.json  BRA.json  NED.json  SWE.json
            ├── NOR.json  JPN.json  AUS.json  COL.json
            ├── ARG.json  ...

database/seeders/
└── ClubProfilesSeeder.php              # Modificado: +reputaciones 80 equipos femeninos

lang/
├── es/
│   ├── cup.php                         # Modificado: +round_of_64
│   └── game.php                        # Modificado: +nombres competiciones femeninas
└── en/
    ├── cup.php                         # Modificado: +Round of 64
    └── game.php                        # Modificado: +women's competition names
```

---

## Comandos de desarrollo

```bash
# Sembrar solo España Femenino
php artisan app:seed-reference-data --season=2026 --country=ESF

# Sembrar solo Bundesliga Femenina (como pool de transferencias)
php artisan app:seed-reference-data --season=2026 --country=DEF

# Ver todos los países disponibles en modo femenino
# ESF, DEF, ENF, FRF, ITF

# Simular una temporada femenina
php artisan app:simulate-season

# Limpiar caché de config
php artisan config:clear
```
