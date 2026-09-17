<?php

namespace App\Http\Middleware;

use App\Models\Kantor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BranchScopeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'cabang' && $user->id_cabang) {
                session(['active_branch_id' => $user->id_cabang]);
            }

            // Share current active branch object to all views
            $activeBranchId = session('active_branch_id');
            $currentBranch = $activeBranchId ? Kantor::find($activeBranchId) : null;
            view()->share('currentBranch', $currentBranch);
            view()->share('allBranches', Kantor::all());
        }

        return $next($request);
    }
}
