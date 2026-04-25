<?php

namespace App\Http\Controllers\SysApp;

use App\Http\Controllers\Controller;
use App\Models\SysAppAdmin;
use App\Models\SysAppAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SysAppAuthController extends Controller
{
    public function login()
    {
        if (session()->has('hoteldesk.sysapp.admin_id')) {
            return redirect()->route('sysapp.hotels.index');
        }

        return view('sysapp.login');
    }

    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ]);

        $email = Str::lower(trim($data['email']));

        $admin = SysAppAdmin::query()
            ->where('email', $email)
            ->first();

        if (! $admin) {
            $this->audit(
                adminId: null,
                action: 'sysapp_login_failed',
                description: 'Intento de acceso con correo inexistente.',
                request: $request,
                meta: ['email' => $email]
            );

            return back()
                ->withErrors(['email' => 'Credenciales inválidas.'])
                ->withInput(['email' => $email]);
        }

        if ($admin->isLocked()) {
            $this->audit(
                adminId: $admin->id,
                action: 'sysapp_login_locked',
                description: 'Intento de acceso durante bloqueo temporal.',
                request: $request,
                meta: [
                    'email' => $email,
                    'locked_until' => optional($admin->locked_until)->toDateTimeString(),
                ]
            );

            return back()
                ->withErrors(['email' => 'La cuenta está bloqueada temporalmente. Intenta más tarde.'])
                ->withInput(['email' => $email]);
        }

        if (! $admin->active) {
            $this->audit(
                adminId: $admin->id,
                action: 'sysapp_login_inactive',
                description: 'Intento de acceso con cuenta inactiva.',
                request: $request,
                meta: ['email' => $email]
            );

            return back()
                ->withErrors(['email' => 'Credenciales inválidas.'])
                ->withInput(['email' => $email]);
        }

        if (! Hash::check($data['password'], $admin->password_hash)) {
            $admin->registerFailedAttempt();

            $this->audit(
                adminId: $admin->id,
                action: 'sysapp_login_failed',
                description: 'Contraseña incorrecta.',
                request: $request,
                meta: [
                    'email' => $email,
                    'failed_attempts' => $admin->failed_attempts,
                    'locked_until' => optional($admin->locked_until)->toDateTimeString(),
                ]
            );

            return back()
                ->withErrors(['email' => 'Credenciales inválidas.'])
                ->withInput(['email' => $email]);
        }

        $admin->resetFailedAttempts();

        $request->session()->regenerate();

        session([
            'hoteldesk.sysapp.admin_id' => $admin->id,
            'hoteldesk.sysapp.admin_name' => $admin->name,
            'hoteldesk.sysapp.admin_role' => $admin->role,
        ]);

        $this->audit(
            adminId: $admin->id,
            action: 'sysapp_login_success',
            description: 'Inicio de sesión correcto.',
            request: $request,
            meta: ['email' => $email]
        );

        return redirect()->route('sysapp.hotels.index');
    }

    public function logout(Request $request)
    {
        $adminId = session('hoteldesk.sysapp.admin_id');

        if ($adminId) {
            $this->audit(
                adminId: $adminId,
                action: 'sysapp_logout',
                description: 'Cierre de sesión SysApp.',
                request: $request
            );
        }

        session()->forget([
            'hoteldesk.sysapp.admin_id',
            'hoteldesk.sysapp.admin_name',
            'hoteldesk.sysapp.admin_role',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sysapp.login');
    }

    private function audit(
        ?int $adminId,
        string $action,
        string $description,
        Request $request,
        array $meta = []
    ): void {
        SysAppAuditLog::create([
            'admin_id' => $adminId,
            'hotel_id' => null,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
            'meta' => $meta ?: null,
            'created_at' => now(),
        ]);
    }
}