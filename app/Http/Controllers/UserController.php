<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'email' => 'required|string|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,usuario',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,usuario',
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
