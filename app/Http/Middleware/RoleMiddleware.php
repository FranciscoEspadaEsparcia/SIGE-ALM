<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        // Si tu user autenticado viene de tabla usuarios y tiene id_rol
        $idRol = $user->id_rol ?? null;

        // roles esperados: ej "1","2"
        if (!$idRol || (!empty($roles) && !in_array((string)$idRol, $roles, true))) {
            abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}
