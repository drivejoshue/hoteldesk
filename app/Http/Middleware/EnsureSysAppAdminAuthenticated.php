<?php

namespace App\Http\Middleware;

use App\Models\SysAppAdmin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSysAppAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminId = session('hoteldesk.sysapp.admin_id');

        if (! $adminId) {
            return redirect()->route('sysapp.login');
        }

        $admin = SysAppAdmin::query()
            ->whereKey($adminId)
            ->where('active', true)
            ->first();

        if (! $admin || $admin->isLocked()) {
            session()->forget([
                'hoteldesk.sysapp.admin_id',
                'hoteldesk.sysapp.admin_name',
                'hoteldesk.sysapp.admin_role',
            ]);

            return redirect()->route('sysapp.login')
                ->withErrors(['email' => 'Sesión no válida.']);
        }

        return $next($request);
    }
}