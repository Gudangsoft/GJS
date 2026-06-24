<?php

namespace App\Http\Middleware;

use App\Models\Journal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJournalAccess
{
    public function handle(Request $request, Closure $next, string $role = 'manager'): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypasses all checks
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $next($request);
        }

        $allowed = match ($role) {
            'manager'  => $user->hasAnyRole(['journal_manager']),
            'editor'   => $user->hasAnyRole(['journal_manager', 'editor']),
            'reviewer' => $user->hasAnyRole(['journal_manager', 'editor', 'reviewer']),
            default    => false,
        };

        if (!$allowed) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk area ini.');
        }

        return $next($request);
    }
}
