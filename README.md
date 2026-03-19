# 🅿️ Esquina Redonda · Sistema de Parqueadero

Sistema de gestión de parqueadero desarrollado en **Laravel 11 + Vue.js 3 + Inertia.js + MySQL**.

---

## ⚙️ Requisitos Previos

Instalar estas herramientas en el PC antes de continuar:

| Herramienta                            | Versión mínima             | Descarga                                    |
| -------------------------------------- | -------------------------- | ------------------------------------------- |
| **PHP**                                | 8.2+                       | Viene incluido con Laragon                  |
| **Composer**                           | 2.x                        | Viene incluido con Laragon                  |
| **Node.js**                            | 18+                        | [nodejs.org](https://nodejs.org)            |
| **MySQL**                              | 8.x                        | Viene incluido con Laragon                  |
| **Laragon** _(recomendado en Windows)_ | Cualquier versión reciente | [laragon.org](https://laragon.org/download) |

> **Consejo:** Si usas **Laragon**, ya incluye PHP, MySQL y Composer. Solo necesitas instalar Node.js por separado.

---

## 🚀 Instalación Paso a Paso

### 1. Clonar el proyecto

```bash
git clone https://github.com/Cristian31306/EsquinaRedonda.git
cd EsquinaRedonda
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de JavaScript

```bash
npm install
```

### 4. Configurar el archivo de entorno

```bash
cp .env.example .env
```

Abrir el archivo `.env` y ajustar la configuración de la base de datos:

```env
APP_NAME="Esquina Redonda"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=esquina_redonda
DB_USERNAME=root
DB_PASSWORD=          # Dejar vacío si usas Laragon por defecto
```

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Crear la base de datos

En **Laragon**: clic derecho en el ícono de la bandeja → **Database** → crear una base de datos llamada `esquina_redonda`.

O desde MySQL en consola:

```sql
CREATE DATABASE esquina_redonda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

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

## 🔄 Actualizar el Sistema (cuando hay cambios)

```bash
git pull
composer install
npm install
php artisan migrate
npm run build
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

**Error de conexión a la base de datos**
→ Verifica que MySQL esté corriendo en Laragon y que el nombre de la BD en `.env` sea correcto.

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
    WshShell.CurrentDirectory = "C:\Users\CANAL ASESORES LTDA\Documents\Proyectos\EsquinaRedonda"
    WshShell.Run """C:\Users\CANAL ASESORES LTDA\.config\herd-lite\bin\php.exe"" artisan serve --host=0.0.0.0 --port=8000", 0, False
    ```

2.  Registra la tarea en **PowerShell** (como administrador):
    ```powershell
    $Action = New-ScheduledTaskAction -Execute 'wscript.exe' -Argument '"C:\Users\CANAL ASESORES LTDA\Documents\Proyectos\EsquinaRedonda\server_silent.vbs"'
    $Trigger = New-ScheduledTaskTrigger -AtLogOn
    Register-ScheduledTask -Action $Action -Trigger $Trigger -TaskName "EsquinaRedondaServer" -Description "Servidor POS Silencioso" -RunLevel Highest -Force
    ```

### Script de Inicio Rápido (.bat)
Si prefieres un archivo manual, crea uno llamado `iniciar.bat` con:
```batch
@echo off
cd /d "C:\Users\CANAL ASESORES LTDA\Documents\Proyectos\EsquinaRedonda"
start /min php artisan serve --host=0.0.0.0 --port=8000
timeout /t 3
start "" "http://localhost:8000"
```
Place it in `shell:startup` for automatic execution on login.
