<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'description' => 'required|string|min:10',
        ]);

        try {
            // Enviando correo al destinatario solicitado
            Mail::to('durancristian31306@gmail.com')->send(new ContactMail($validated));

            return back()->with('success', '¡Gracias por contactarnos! Te responderemos lo antes posible.');
        } catch (\Exception $e) {
            \Log::error("Error enviando correo: " . $e->getMessage());
            return back()->with('error', 'Lo sentimos, hubo un problema al enviar tu mensaje. Por favor intenta por WhatsApp.');
        }
    }
}
