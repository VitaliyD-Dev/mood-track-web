<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            Log::info('User is authenticated, updating last_seen.');
            Auth::user()->update(['last_seen' => Carbon::now()]);
        }

        return $next($request);
    }

}
