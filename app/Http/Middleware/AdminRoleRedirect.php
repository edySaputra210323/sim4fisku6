<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminRoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            // hanya role admin yang boleh akses panel admin
            if (! $user->hasRole('admin')) {
                Auth::logout();

                return redirect()
                    ->route('filament.pegawai.auth.login') // arahkan ke login panel pegawai
                    ->withErrors([
                        'email' => 'Akun anda tidak memiliki akses ke panel admin.',
                    ]);
            }
        }

        return $next($request);
    }
}
