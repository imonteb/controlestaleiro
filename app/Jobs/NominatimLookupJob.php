<?php

namespace App\Jobs;

use App\Models\NominatimCache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NominatimLookupJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public readonly string $query,
        public readonly string $dd,
        public readonly string $cc,
        public readonly string $concelhoDesig,
    ) {}

    public function handle(): void
    {
        $hash = NominatimCache::buildHash($this->query, $this->dd, $this->cc);

        if (NominatimCache::where('search_hash', $hash)->exists()) {
            return;
        }

        $response = Http::timeout(10)
            ->withHeaders(['User-Agent' => config('app.name').'/'.config('app.version').' '.config('services.nominatim.contact')])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => "{$this->query}, {$this->concelhoDesig}, Portugal",
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'limit' => 1,
                'countrycodes' => 'pt',
            ]);

        if (! $response->successful()) {
            Log::warning('Nominatim lookup failed', ['query' => $this->query, 'status' => $response->status()]);

            return;
        }

        $results = $response->json();

        if (empty($results)) {
            return;
        }

        $place = $results[0];
        $address = $place['address'] ?? [];

        NominatimCache::store([
            'search_hash' => $hash,
            'query_text' => $this->query,
            'dd' => $this->dd,
            'cc' => $this->cc,
            'road' => $address['road'] ?? null,
            'suburb' => $address['suburb'] ?? $address['quarter'] ?? null,
            'localidade' => $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
            'postcode' => $address['postcode'] ?? null,
            'lat' => $place['lat'] ?? null,
            'lon' => $place['lon'] ?? null,
            'osm_type' => isset($place['osm_type']) ? strtoupper(substr($place['osm_type'], 0, 1)) : null,
            'osm_id' => $place['osm_id'] ?? null,
            'source' => 'nominatim',
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('NominatimLookupJob failed', [
            'query' => $this->query,
            'dd' => $this->dd,
            'cc' => $this->cc,
            'error' => $e->getMessage(),
        ]);
    }
}
