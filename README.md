# 🅿️ ParkiApp · Sistema de Parqueadero

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
> **Recomendación:** Se sugiere instalar el proyecto en `C:\laragon\www\ParkiApp` para una mejor integración con Laragon.

```bash
# Navegar a la carpeta www de Laragon (ejemplo)
cd C:\laragon\www

# Clonar el proyecto
git clone https://github.com/Cristian31306/ParkiApp.git
cd ParkiApp
```

### 2. Instalación rápida (Script Automático)

Si estás en **Windows**, ejecuta este archivo para instalar las herramientas necesarias (Laragon, Node) y configurar el proyecto automáticamente:

```bash
configurar.bat
```

Si ya tienes las herramientas instaladas o estás en **Linux/Mac**, usa:

```bash
php instalar.php
```

---

### 4. Configurar el archivo de entorno

```bash
cp .env.example .env
```

Abrir el archivo `.env` y asegúrate de que la conexión sea `sqlite`:

```env
APP_NAME="ParkiApp"
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

Para actualizar el sistema con los últimos cambios, ejecuta el script de actualización:

```bash
php actualizar.php
```

---

## 📁 Estructura Resumida

```
ParkiApp/
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

**El backup no llega a Telegram**
→ Asegúrate de que el servicio **ParkiAppQueue** esté instalado y en estado 'Started'. Puedes revisar su estado con comando en la sección de mantenimiento.

---

---

## 🖥️ Configuración para Producción (Windows + Auto-inicio)

Para que el sistema esté siempre activo y se inicie automáticamente en segundo plano al encender el PC, utilizamos **NSSM** (Non-Sucking Service Manager). Esto garantiza que el servidor se reinicie automáticamente si falla o si el PC se reinicia.

### 1. Requisitos para el PC Servidor
- **PHP** (v8.2+) y **Composer** instalados.
- **Node.js** (v18+) instalado.
- El proyecto debe estar en una carpeta permanente (ej. `C:\ParkiApp`).
- **NSSM**: Ya incluido en la carpeta `nssm_bin` del proyecto.

### 2. Instalación de Servicios
Abre una terminal (**PowerShell** o **CMD**) como **Administrador** y ejecuta los siguientes comandos desde la carpeta raíz del proyecto:

#### A. Servicio del Servidor (Interfaz Web)
Este servicio mantiene activo el motor de Laravel en el puerto 8000.
```powershell
.\nssm_bin\nssm.exe install ParkiAppServer (Get-Command php).Source "artisan serve --host=0.0.0.0 --port=8000"
.\nssm_bin\nssm.exe set ParkiAppServer AppDirectory (Get-Location).Path
.\nssm_bin\nssm.exe start ParkiAppServer
```

#### B. Servicio de Procesamiento (Colas y Telegram)
Este servicio es **obligatorio** para que los respaldos lleguen a Telegram y se procesen tareas en segundo plano.
```powershell
.\nssm_bin\nssm.exe install ParkiAppQueue (Get-Command php).Source "artisan queue:work"
.\nssm_bin\nssm.exe set ParkiAppQueue AppDirectory (Get-Location).Path
.\nssm_bin\nssm.exe start ParkiAppQueue
```

### 3. Apertura Automática del Navegador
Si deseas que el navegador se abra automáticamente al iniciar sesión en Windows:
1. Presiona `Win + R`, escribe `shell:startup` y presiona Enter.
2. Crea un acceso directo a tu navegador (ej. Chrome) apuntando a `http://localhost:8000`.

---

## 📡 Acceso desde Otros Dispositivos
Una vez instalado el servicio, cualquier dispositivo en la misma red WiFi puede entrar usando la IP del PC servidor:
1. En el PC servidor, escribe `ipconfig` en la terminal para ver tu IP (ej. `192.168.1.50`).
2. En tu celular o tablet, entra a: `http://192.168.1.50:8000`.

---

## 🛠️ Comandos de Mantenimiento
Si necesitas detener o reiniciar los servicios:
- **Estado:** `.\nssm_bin\nssm.exe status ParkiAppServer`
- **Reiniciar:** `.\nssm_bin\nssm.exe restart ParkiAppServer`
- **Eliminar:** `.\nssm_bin\nssm.exe remove ParkiAppServer confirm`

---

Para actualizar el sistema con los últimos cambios:
```bash
php actualizar.php
# Luego reinicia los servicios:
.\nssm_bin\nssm.exe restart ParkiAppServer
.\nssm_bin\nssm.exe restart ParkiAppQueue
```

---

&copy; 2026 ParkiApp SyS - Sistema Profesional de Gestión de Parqueaderos.
