<?php

namespace App\Jobs;

use App\Models\UserSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeolocateSessionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 10;

    public function __construct(
        public readonly string $sessionId,
        public readonly string $ip,
    ) {
        $this->onQueue('low');
    }

    public function handle(): void
    {
        $log = UserSession::where('session_id', $this->sessionId)->first();

        if (! $log) {
            return;
        }

        // Skip local IPs
        if (in_array($this->ip, ['127.0.0.1', '::1'], true)) {
            $log->update([
                'city' => 'Localhost',
                'region' => 'Local',
                'country' => 'Local',
            ]);

            return;
        }

        try {
            $response = Http::timeout(8)->get("http://ip-api.com/json/{$this->ip}");

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'success') {
                    $log->update([
                        'city' => $data['city'] ?? null,
                        'region' => $data['regionName'] ?? null,
                        'country' => $data['country'] ?? null,
                        'latitude' => $data['lat'] ?? null,
                        'longitude' => $data['lon'] ?? null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('GeolocateSessionJob failed: '.$e->getMessage(), [
                'session_id' => $this->sessionId,
                'ip' => $this->ip,
            ]);
        }
    }
}
