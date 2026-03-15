# Desplegar SheManager en Railway

## Pasos (15 minutos)

### 1. Sube el código a GitHub

```bash
# En la carpeta virtua-fc-futfem-web/
git init
git add .
git commit -m "SheManager initial deploy"
git remote add origin https://github.com/izandhh15/shemanager
git push -u origin main
```

### 2. Crea el proyecto en Railway

1. Ve a [railway.app](https://railway.app) → **New Project**
2. Elige **Deploy from GitHub repo**
3. Selecciona `izandhh15/shemanager`
4. Railway detecta automáticamente el `nixpacks.toml` y construye la app

### 3. Añade PostgreSQL

1. En tu proyecto Railway → **+ New** → **Database** → **PostgreSQL**
2. Railway añade `DATABASE_URL` automáticamente a tu servicio

### 4. Configura las variables de entorno

En Railway → tu servicio web → **Variables**, añade:

| Variable | Valor |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Genera con: `php artisan key:generate --show` |
| `APP_URL` | La URL que Railway te asigna (ej: `https://shemanager-prod.up.railway.app`) |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | `${{DATABASE_URL}}` (referencia a la DB de Railway) |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `LOG_CHANNEL` | `stderr` |
| `BETA_MODE` | `false` |
| `ALLOW_NEW_SEASON` | `true` |

> **Generar APP_KEY:** en local ejecuta `php artisan key:generate --show` y copia el resultado.

### 5. El startup script hace el resto automáticamente

El archivo `railway-start.sh` hace al arrancar:
- ✅ Ejecuta migraciones (`php artisan migrate --force`)
- ✅ Siembra los datos de fútbol femenino 2026 (solo si la BD está vacía)
- ✅ Cachea config, rutas y vistas
- ✅ Arranca queue worker en background
- ✅ Arranca el servidor web

### 6. Verifica el deploy

Cuando Railway diga **"Active"**, visita tu URL. Deberías ver la pantalla de login de SheManager.

### 7. Crea tu primera partida

1. Regístrate con cualquier email
2. Elige un equipo de **Liga F** (temporada 2026)
3. ¡A jugar!

---

## Solución de problemas

### Error: "No application encryption key"
→ Falta `APP_KEY`. Genera una con `php artisan key:generate --show` y añádela en Variables.

### Error: "SQLSTATE: connection refused"
→ Falta la variable `DB_URL`. Asegúrate de añadir PostgreSQL al proyecto y referenciar `${{DATABASE_URL}}`.

### La base de datos está vacía tras el deploy
→ El seeder tardó. En Railway → tu servicio → **Logs** busca "Seeding complete". Si no aparece, ve a **Settings** → **Redeploy**.

### El queue worker no procesa trabajos
→ El `railway-start.sh` lo arranca en background. Si necesitas más capacidad, añade un segundo servicio en Railway con el comando: `php artisan queue:work --sleep=3 --tries=3`

---

## Dominio personalizado (shemanager.com)

1. Railway → tu servicio → **Settings** → **Networking** → **Custom Domain**
2. Añade `app.shemanager.com`
3. Railway te da los registros DNS (CNAME) para añadir en tu proveedor
4. Actualiza `APP_URL=https://app.shemanager.com` en Variables
