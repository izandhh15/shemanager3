# Desplegar SheManager en Koyeb + Neon
## 100% gratis · Sin tarjeta · Solo navegador

---

## Paso 1 — Crea la base de datos en Neon (2 minutos)

1. Ve a **[neon.tech](https://neon.tech)** → **Sign up** con GitHub (gratis, sin tarjeta)
2. **Create project** → nombre: `shemanager` → region: **EU Central (Frankfurt)**
3. En el dashboard verás una cadena de conexión. Copia el **Connection string** completo:
   ```
   postgresql://shemanager:xxxx@ep-xxx.eu-central-1.aws.neon.tech/shemanager?sslmode=require
   ```
   ⚠️ **Guarda este string**, lo necesitarás en el paso 3.

---

## Paso 2 — Sube el código a GitHub

Asegúrate de que `github.com/izandhh15/shemanager` tiene el código del ZIP con estos archivos en la raíz:
- `Dockerfile`
- `railway-start.sh`
- `render.yaml` (se puede ignorar)
- todo el proyecto Laravel

---

## Paso 3 — Despliega en Koyeb (5 minutos)

1. Ve a **[app.koyeb.com](https://app.koyeb.com)** → **Sign up** con GitHub (gratis, sin tarjeta)

2. **Create App** → **GitHub** → selecciona `izandhh15/shemanager`

3. En la configuración del servicio:
   - **Builder**: Docker
   - **Dockerfile path**: `Dockerfile`
   - **Port**: `8080`
   - **Region**: Frankfurt

4. En **Environment Variables** añade estas variables:

| Variable | Valor |
|---|---|
| `APP_NAME` | `SheManager` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Genera en: [generate-random.org/laravel-key-generator](https://generate-random.org/laravel-key-generator) |
| `APP_URL` | `https://shemanager-izandhh.koyeb.app` (la URL que Koyeb te asigne) |
| `DB_CONNECTION` | `pgsql` |
| `DATABASE_URL` | El connection string de Neon del paso 1 |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `error` |
| `BETA_MODE` | `false` |
| `ALLOW_NEW_SEASON` | `true` |
| `PORT` | `8080` |

5. **Deploy** → Koyeb construye el Docker (~4 minutos)

---

## Paso 4 — Ya está

Koyeb te da una URL como `https://shemanager-izandhh.koyeb.app`.

El startup script automáticamente:
- ✅ Migra la base de datos
- ✅ Siembra todos los equipos de fútbol femenino (Liga F, Bundesliga, WSL, etc.)
- ✅ Arranca el servidor

Regístrate, elige un equipo de **Liga F** y empieza a jugar.

---

## Plan gratuito de Koyeb incluye
- ✅ 1 servicio web activo
- ✅ Sin tarjeta requerida
- ✅ SSL automático
- ✅ Dominio `.koyeb.app` gratis
- ✅ Deploy automático en cada push a GitHub

## Plan gratuito de Neon incluye
- ✅ 1 proyecto PostgreSQL
- ✅ 512 MB almacenamiento
- ✅ Sin tarjeta requerida
- ✅ Suspensión automática cuando no se usa (ahorra recursos)
