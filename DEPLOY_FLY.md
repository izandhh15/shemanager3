# Desplegar SheManager en Fly.io

## Requisitos
- Cuenta en [fly.io](https://fly.io) (gratis, solo necesitas email)
- [flyctl](https://fly.io/docs/hands-on/install-flyctl/) instalado

```bash
# Instalar flyctl (Mac/Linux)
curl -L https://fly.io/install.sh | sh

# Windows (PowerShell)
pwsh -Command "iwr https://fly.io/install.ps1 -useb | iex"
```

---

## Pasos

### 1. Login en Fly.io
```bash
fly auth login
```

### 2. Sube el proyecto y lanza
```bash
cd virtua-fc-futfem-web/

# Primera vez — crea la app
fly launch --no-deploy --name shemanager --region mad
```

Cuando pregunte si usar el `fly.toml` existente → **Yes**.

### 3. Crea la base de datos PostgreSQL (gratis)
```bash
fly postgres create --name shemanager-db --region mad --vm-size shared-cpu-1x --volume-size 1
fly postgres attach shemanager-db --app shemanager
```
Esto añade `DATABASE_URL` automáticamente a tu app.

### 4. Crea el volumen de storage
```bash
fly volumes create shemanager_storage --region mad --size 1 --app shemanager
```

### 5. Configura los secrets (variables de entorno)
```bash
# Genera APP_KEY
php artisan key:generate --show
# Copia el resultado y úsalo aquí:

fly secrets set \
  APP_KEY="base64:TU_CLAVE_AQUI" \
  APP_URL="https://shemanager.fly.dev" \
  DB_CONNECTION="pgsql" \
  --app shemanager
```

### 6. Despliega
```bash
fly deploy --app shemanager
```

El build tarda ~4 minutos la primera vez. Fly construye el Docker, migra la BD y siembra los datos de fútbol femenino automáticamente.

### 7. Abre la app
```bash
fly open --app shemanager
```

---

## URL resultante
`https://shemanager.fly.dev`

---

## Dominio personalizado (opcional)
```bash
fly certs add app.shemanager.com --app shemanager
```
Luego añade el CNAME en tu DNS apuntando a `shemanager.fly.dev`.

---

## Comandos útiles
```bash
# Ver logs en tiempo real
fly logs --app shemanager

# Ejecutar comandos artisan
fly ssh console --app shemanager -C "php artisan tinker"

# Re-sembrar datos
fly ssh console --app shemanager -C "php artisan app:seed-reference-data --season=2026 --fresh"

# Escalar a 0 cuando no uses (ahorra créditos)
fly scale count 0 --app shemanager

# Volver a encender
fly scale count 1 --app shemanager
```

---

## Plan gratuito de Fly.io incluye
- ✅ 3 VMs shared compartidas
- ✅ 3 GB de volumen
- ✅ PostgreSQL shared gratis
- ✅ SSL automático
- ✅ Dominio `.fly.dev` gratis
