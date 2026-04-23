<?php

namespace App\Http\Middleware;

use App\Jobs\GeolocateSessionJob;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogSessionActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = session()->getId();
            $ip = $request->ip();

            $log = UserSession::where('session_id', $sessionId)->first();

            if (! $log) {
                UserSession::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'ip_address' => $ip,
                    'user_agent' => $request->userAgent(),
                    'login_at' => now(),
                    'last_activity_at' => now(),
                ]);

                GeolocateSessionJob::dispatch($sessionId, $ip);
            } else {
                $log->update([
                    'last_activity_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
