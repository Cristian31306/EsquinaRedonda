<x-mail::message>
# Nuevo Mensaje de Contacto

Has recibido un nuevo mensaje desde el formulario de la Landing Page de **ParkiApp**.

**Detalles del remitente:**
*   **Nombre:** {{ $data['name'] }}
*   **Correo electrónico:** {{ $data['email'] }}
*   **Teléfono:** {{ $data['phone'] }}

**Mensaje:**
{{ $data['description'] }}

Gracias,<br>
{{ config('app.name') }} por **Algorah**
</x-mail::message>
