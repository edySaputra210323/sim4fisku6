<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PegawaiRoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $routeName = $request->route()?->getName();

        if ($user) {
            // hanya role guru yang boleh akses panel pegawai
            if (! $user->hasRole('guru')) {
                Auth::logout();

                return redirect()
                    ->route('filament.admin.auth.login') // atau route lain sesuai kebutuhan
                    ->withErrors([
                        'email' => 'Akun anda tidak memiliki akses ke panel pegawai.',
                    ]);
            }
        }

        return $next($request);
    }
}
