<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Mail\SupportTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;

class SupportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = SupportTicket::with(['user', 'tenant'])
            ->orderBy('last_reply_at', 'desc')
            ->orderBy('created_at', 'desc');

        // El trait BelongsToTenant ya filtra por tenant si el usuario no es Super Admin
        // Pero el Super Admin debe ver todo sin filtros.
        if ($user->role === 'super_admin') {
            $tickets = SupportTicket::withoutGlobalScope('tenant')
                ->with(['user', 'tenant'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $tickets = $query->get();
        }

        return Inertia::render('Support/Index', [
            'tickets' => $tickets
        ]);
    }

    public function create()
    {
        return Inertia::render('Support/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
            'last_reply_at' => Carbon::now(),
        ]);

        $message = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Notificar al Super Admin
        try {
            Mail::to('durancristian31306@gmail.com')->send(new SupportTicketMail($ticket, $message, false));
        } catch (\Exception $e) {
            \Log::error("Error enviando correo de soporte: " . $e->getMessage());
        }

        return redirect()->route('support.show', $ticket->id)->with('success', 'Ticket creado correctamente.');
    }

    public function show(SupportTicket $ticket)
    {
        // Verificar acceso (Super Admin ve todo, Admin ve los suyos)
        if (auth()->user()->role !== 'super_admin' && $ticket->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        return Inertia::render('Support/Show', [
            'ticket' => $ticket->load(['user', 'tenant', 'messages.user']),
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        $ticket->update(['last_reply_at' => Carbon::now()]);

        $ticket->update(['last_reply_at' => Carbon::now()]);

        if (auth()->user()->role === 'super_admin') {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Respuesta enviada.');
    }

    public function close(SupportTicket $ticket)
    {
        $ticket->update(['status' => 'closed']);
        return back()->with('success', 'Ticket cerrado correctamente.');
    }
}
