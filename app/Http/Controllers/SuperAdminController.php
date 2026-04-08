<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Tenants/Index', [
            'tenants' => Tenant::withCount('users')->get(),
        ]);
    }

    public function storeTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tenants',
            'plan' => 'required|in:basico,pro',
            'billing_cycle' => 'required|in:mensual,anual',
            'expires_at' => 'nullable|date',
            'status' => 'required|in:active,suspended',
            'nit' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'social_handle' => 'nullable|string|max:50',
            'tax_regime' => 'nullable|string|max:100',
            'business_hours' => 'nullable|string|max:100',
            'welcome_message' => 'nullable|string',
            'disclaimer_message' => 'nullable|string',
        ]);

        Tenant::create($request->all() + ['slug' => Str::slug($request->name)]);

        return back()->with('success', 'Empresa creada correctamente.');
    }

    public function updateTenant(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tenants,name,' . $tenant->id,
            'plan' => 'required|in:basico,pro',
            'billing_cycle' => 'required|in:mensual,anual',
            'expires_at' => 'nullable|date',
            'nit' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'social_handle' => 'nullable|string|max:50',
            'tax_regime' => 'nullable|string|max:100',
            'business_hours' => 'nullable|string|max:100',
            'welcome_message' => 'nullable|string',
            'disclaimer_message' => 'nullable|string',
        ]);

        $tenant->update($request->all() + ['slug' => Str::slug($request->name)]);

        return back()->with('success', 'Datos de la empresa actualizados.');
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update([
            'status' => $tenant->status === 'active' ? 'suspended' : 'active',
        ]);

        return back()->with('success', 'Estado de la empresa actualizado.');
    }

    public function manageUsers(Tenant $tenant)
    {
        return Inertia::render('Admin/Tenants/Users', [
            'tenant' => $tenant,
            'users' => User::withoutGlobalScopes()->where('tenant_id', $tenant->id)->get(),
        ]);
    }

    public function addUser(Tenant $tenant, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
        ]);

        $email = $request->email;
        if (!str_contains($email, '@')) {
            $domain = str_replace('-', '', $tenant->slug);
            $email .= '@' . $domain . '.com';
        }

        // Restricción de Plan Básico: Máximo 3 usuarios
        if ($tenant->plan === 'basico') {
            $userCount = User::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();
            if ($userCount >= 3) {
                return back()->with('error', 'El Plan Básico tiene un límite de 3 usuarios. Sube a Pro para agregar más personal.');
            }
        }

        // Verificar unicidad después de formatear
        if (User::withGlobalScope('tenant', function(){})->where('email', $email)->exists()) {
            return back()->with('error', 'El correo ' . $email . ' ya está en uso.');
        }

        User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]);

        return back()->with('success', 'Usuario añadido como ' . $email);
    }

    public function resetUserPassword(User $user, Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña restablecida correctamente.');
    }

    public function deleteUser(User $user)
    {
        // No permitir borrarte a ti mismo si llegas por aquí (aunque eres super_admin)
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
