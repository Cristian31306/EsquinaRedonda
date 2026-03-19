# 🅿️ Esquina Redonda · Sistema de Parqueadero

Sistema de gestión de parqueadero desarrollado en **Laravel 11 + Vue.js 3 + Inertia.js + SQLite**.

---

## ⚙️ Requisitos Previos

Instalar estas herramientas en el PC antes de continuar:

| Herramienta                            | Versión mínima             | Descarga                                    |
| -------------------------------------- | -------------------------- | ------------------------------------------- |
| **PHP**                                | 8.2+                       | Viene incluido con Laragon                  |
| **Composer**                           | 2.x                        | Viene incluido con Laragon                  |
| **Node.js**                            | 18+                        | [nodejs.org](https://nodejs.org)            |
| **Laragon** _(recomendado en Windows)_ | Cualquier versión reciente | [laragon.org](https://laragon.org/download) |

> **Consejo:** Si usas **Laragon**, ya incluye PHP y Composer. Solo necesitas instalar Node.js por separado. SQLite ya está integrado en PHP.

### 🔧 Instalación Rápida de Herramientas (Windows)

Si estás en un PC nuevo, puedes instalar todo lo necesario ejecutando este comando en la terminal (**PowerShell** como Administrador):

```powershell
winget install Laragon.Laragon OpenJS.NodeJS.LTS
```

---

## 🚀 Instalación Paso a Paso

### 1. Clonar el proyecto

> [!TIP]
> **Recomendación:** Se sugiere instalar el proyecto en `C:\laragon\www\EsquinaRedonda` para una mejor integración con Laragon.

```bash
# Navegar a la carpeta www de Laragon (ejemplo)
cd C:\laragon\www

# Clonar el proyecto
git clone https://github.com/Cristian31306/EsquinaRedonda.git
cd EsquinaRedonda
```

### 2. Instalación rápida (Comando único)

Ejecuta este comando en la terminal para instalar todo automáticamente (dependencias de PHP, JS, base de datos y compilación):

```bash
composer install && npm install && php artisan key:generate && php artisan migrate --seed && npm run build
```
O si prefieres ir paso a paso:
1. `composer install`
2. `npm install`
3. `cp .env.example .env` (y configurar `DB_CONNECTION=sqlite`)
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `npm run build`

### 4. Configurar el archivo de entorno

```bash
cp .env.example .env
```

Abrir el archivo `.env` y asegúrate de que la conexión sea `sqlite`:

```env
APP_NAME="Esquina Redonda"
APP_URL=http://localhost

DB_CONNECTION=sqlite
# No necesitas configurar host, puerto, usuario ni contraseña para SQLite.
```

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 5. Base de Datos (SQLite)

El sistema utiliza **SQLite**, por lo que no necesitas instalar ni configurar un servidor de base de datos externo (como MySQL). 

- El archivo de la base de datos se guarda en `database/database.sqlite`.
- Si el archivo no existe, Laravel lo creará automáticamente al ejecutar las migraciones.
- Asegúrate de que el archivo `database/database.sqlite` tenga permisos de escritura.

### 7. Ejecutar migraciones y datos iniciales

```bash
php artisan migrate --seed
```

Esto crea todas las tablas y registra:

- ✅ Usuario administrador: `admin` / `123456`
- ✅ Tarifas base para moto, carro y pesado

### 8. Compilar los archivos del frontend

```bash
npm run build
```

### 9. Iniciar el servidor

```bash
php artisan serve
```

El sistema estará disponible en: **http://localhost:8000**

---

## 🔑 Acceso al Sistema

| Campo          | Valor    |
| -------------- | -------- |
| **Usuario**    | `admin`  |
| **Contraseña** | `123456` |

---

## 🖨️ Impresora Térmica

El sistema genera tickets optimizados para impresoras térmicas de **58mm**. Al registrar un vehículo, el ticket se imprime automáticamente a través del diálogo de impresión del navegador.

Configura la impresora como impresora predeterminada del sistema para que el proceso sea fluido.

---

## 📡 Acceso desde Otros Dispositivos en la Misma Red

Para que otros equipos, tablets o celulares en la misma red WiFi accedan al sistema:

1. Obtener la IP local del PC servidor:

    ```
    ipconfig   (en Windows)
    ```

    Buscar algo como `192.168.1.X`

2. Iniciar el servidor escuchando en todas las interfaces:

    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```

3. Desde cualquier otro dispositivo en la red, abrir el navegador e ir a:
    ```
    http://192.168.1.X:8000
    ```

---

Para actualizar el sistema con los últimos cambios, ejecuta este comando único:

```bash
git pull && composer install && npm install && php artisan migrate --force && npm run build
```

---

## 📁 Estructura Resumida

```
EsquinaRedonda/
├── app/
│   ├── Http/Controllers/     # Controladores (Tickets, Caja, Membresías...)
│   ├── Models/               # Modelos (Vehicle, Ticket, Payment, Membership...)
│   └── Services/             # BillingService (cálculo de tarifas)
├── database/
│   ├── migrations/           # Estructura de la BD
│   └── seeders/              # Datos iniciales (admin, tarifas)
├── resources/js/
│   ├── Pages/                # Vistas Vue.js (Tickets, Shifts, Memberships...)
│   └── Layouts/              # Layout principal con sidebar
└── routes/web.php            # Rutas del sistema
```

---

## 🛠️ Solución de Problemas Comunes

**Error `php: command not found`**
→ Asegúrate de que Laragon o PHP estén en el PATH del sistema. Reinicia la terminal después de instalar.

**Error de base de datos**
→ Verifica que el archivo `database/database.sqlite` exista y tenga permisos de escritura. Asegúrate de que `DB_CONNECTION=sqlite` esté en tu `.env`.

**Página en blanco después de `npm run build`**
→ Ejecuta `php artisan config:clear && php artisan cache:clear`.

**El ticket no imprime**
→ Verifica que la impresora esté como predeterminada y que el navegador tenga permisos de impresión.

---

## 🖥️ Configuración para Producción (Windows + Auto-inicio)

Para que el sistema esté siempre activo y se inicie automáticamente en segundo plano al encender el PC:

### Inicio Automático Invisible (Recomendado)

Para que el servidor corra en el fondo sin ventanas que se puedan cerrar por error:

1.  Crea un archivo llamado `server_silent.vbs` en la carpeta raíz del proyecto y pega esto:
    ```vbs
    Set WshShell = CreateObject("WScript.Shell")
    ' Obtiene automáticamente la carpeta desde la que se ejecuta el script
    strPath = CreateObject("Scripting.FileSystemObject").GetParentFolderName(WScript.ScriptFullName)
    WshShell.CurrentDirectory = strPath
    ' Ejecuta el servidor usando el comando php (debe estar en el PATH)
    WshShell.Run "php artisan serve --host=0.0.0.0 --port=8000", 0, False
    ```

2.  Registra la tarea en **PowerShell** (como administrador):
    ```powershell
    # Define la ruta del proyecto (ajusta si lo instalaste en otro lugar)
    $ProjectDir = "C:\laragon\www\EsquinaRedonda"
    $Action = New-ScheduledTaskAction -Execute 'wscript.exe' -Argument "`"$ProjectDir\server_silent.vbs`""
    $Trigger = New-ScheduledTaskTrigger -AtLogOn
    Register-ScheduledTask -Action $Action -Trigger $Trigger -TaskName "EsquinaRedondaServer" -Description "Servidor POS Silencioso" -RunLevel Highest -Force
    ```

### Script de Inicio Rápido (.bat)
Si prefieres un archivo manual, crea uno llamado `iniciar.bat` con:
```batch
@echo off
cd /d "%~dp0"
start /min php artisan serve --host=0.0.0.0 --port=8000
timeout /t 3
start "" "http://localhost:8000"
```
Place it in `shell:startup` for automatic execution on login.
