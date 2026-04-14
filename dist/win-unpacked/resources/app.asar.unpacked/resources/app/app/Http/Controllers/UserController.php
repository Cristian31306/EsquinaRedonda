<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $users = User::select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->orderBy('name')
            ->get();

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:admin,operator,usuario',
        ]);

        $email = $request->email;
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return back()->with('error', 'Error: No se encontró una empresa vinculada a tu cuenta de administrador.');
        }

        if (!str_contains($email, '@')) {
            $domain = str_replace('-', '', $tenant->slug);
            $email .= '@' . $domain . '.com';
        }

        // Restricción de Plan: Validar límite de usuarios
        $userCount = User::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();
        if ($userCount >= $tenant->max_users) {
            return back()->with('error', "Tu plan actual está limitado a {$tenant->max_users} usuarios. Sube al Plan Profesional para usuarios ilimitados.");
        }

        if (User::withoutGlobalScopes()->where('email', $email)->exists()) {
            return back()->with('error', 'El correo ' . $email . ' ya existe.');
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'tenant_id' => (string) $tenant->id,
                'is_active' => true,
            ]);

            return redirect()->route('users.index')->with('success', "Usuario creado como {$email}");
        } catch (\Exception $e) {
            logger()->error("Error creando usuario: " . $e->getMessage());
            return back()->with('error', 'Error al guardar el usuario en la nube: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,operator,usuario',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) abort(403);

        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes desactivar tu propia cuenta.']);
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->route('users.index')->with('success', "Usuario {$status} correctamente.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) abort(403);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes desactivar tu propia cuenta.']);
        }

        // We use deactivation instead of deletion as per user request
        $user->update(['is_active' => false]);

        return redirect()->route('users.index')->with('success', 'Usuario desactivado correctamente.');
    }
}
