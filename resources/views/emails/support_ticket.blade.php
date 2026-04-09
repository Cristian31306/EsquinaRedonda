@component('mail::message')
# Hola, {{ $isReply ? 'Admin' : 'Equipo de Soporte' }}

{{ $isReply ? 'Se ha recibido una nueva respuesta en el ticket:' : 'Se ha abierto un nuevo ticket de soporte:' }}

**Asunto:** {{ $ticket->subject }}  
**Prioridad:** {{ strtoupper($ticket->priority) }}  
**Usuario:** {{ $ticket->user->name }} ({{ $ticket->tenant->name ?? 'N/A' }})

---

**Mensaje:**  
{{ $messageObj->message ?? 'Sin contenido adicional' }}

---

@component('mail::button', ['url' => route('support.show', $ticket->id)])
Ver Ticket Completo
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
