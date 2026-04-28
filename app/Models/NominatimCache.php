<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NominatimCache extends Model
{
    protected $table = 'nominatim_cache';

    protected $fillable = [
        'search_hash',
        'query_text',
        'dd',
        'cc',
        'road',
        'suburb',
        'localidade',
        'postcode',
        'lat',
        'lon',
        'osm_type',
        'osm_id',
        'hit_count',
        'source',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lon' => 'float',
            'hit_count' => 'integer',
            'last_used_at' => 'datetime',
        ];
    }

    public static function buildHash(string $query, string $dd, string $cc): string
    {
        return hash('sha256', mb_strtolower(trim($query)).'|'.$dd.'|'.$cc);
    }

    public static function searchLocal(string $query, string $dd, string $cc, int $limit = 10): array
    {
        $words = array_filter(explode(' ', mb_strtolower(trim($query))));

        $q = self::where('dd', $dd)->where('cc', $cc)->whereNotNull('road');

        foreach ($words as $word) {
            $q->where(function ($sub) use ($word) {
                $sub->where('road', 'like', "%{$word}%")
                    ->orWhere('suburb', 'like', "%{$word}%");
            });
        }

        return $q->orderByDesc('hit_count')
            ->limit($limit)
            ->get(['road', 'suburb', 'localidade', 'postcode'])
            ->toArray();
    }

    /**
     * Road-like OSM keys accepted as a valid street name.
     * Madeira has many paths, footways and pedestrian streets not tagged as "road".
     */
    private const ROAD_KEYS = ['road', 'path', 'footway', 'pedestrian', 'residential', 'cycleway', 'track', 'service'];

    private static function extractRoad(array $address): ?string
    {
        foreach (self::ROAD_KEYS as $key) {
            if (! empty($address[$key])) {
                return $address[$key];
            }
        }

        return null;
    }

    private static function nominatimHeaders(): array
    {
        return [
            'User-Agent' => config('app.name').'/'.config('app.version').' '.config('services.nominatim.contact'),
            'Accept-Language' => 'pt-PT,pt;q=0.9',
        ];
    }

    private static function parseNominatimResults(array $json, string $query, string $dd, string $cc): array
    {
        $results = [];
        $seen = [];

        foreach ($json as $place) {
            $address = $place['address'] ?? [];
            $road = self::extractRoad($address);

            if (! $road) {
                continue;
            }

            $key = mb_strtolower($road);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $item = [
                'road' => $road,
                'suburb' => $address['suburb'] ?? $address['quarter'] ?? null,
                'localidade' => $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['municipality'] ?? null,
                'postcode' => $address['postcode'] ?? null,
            ];

            $hash = self::buildHash($road, $dd, $cc);

            self::firstOrCreate(
                ['search_hash' => $hash],
                array_merge($item, [
                    'query_text' => $query,
                    'dd' => $dd,
                    'cc' => $cc,
                    'lat' => $place['lat'] ?? null,
                    'lon' => $place['lon'] ?? null,
                    'osm_type' => isset($place['osm_type']) ? strtoupper(substr($place['osm_type'], 0, 1)) : null,
                    'osm_id' => $place['osm_id'] ?? null,
                    'source' => 'nominatim',
                    'last_used_at' => now(),
                ])
            );

            $results[] = $item;
        }

        return $results;
    }

    private static function nominatimRequest(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout(8)->withHeaders(self::nominatimHeaders());
    }

    public static function searchNominatim(string $query, string $dd, string $cc, string $concelhoDesig): array
    {
        try {
            // Structured search: Nominatim interprets "street" separately from location context,
            // giving much better partial-name matching than a single freeform "q=" string.
            $response = self::nominatimRequest()->get('https://nominatim.openstreetmap.org/search', [
                'street' => $query,
                'county' => $concelhoDesig,
                'state' => 'Madeira',
                'country' => 'pt',
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'limit' => 10,
                'countrycodes' => 'pt',
            ]);

            if ($response->successful()) {
                $results = self::parseNominatimResults($response->json(), $query, $dd, $cc);

                if (! empty($results)) {
                    return $results;
                }
            }

            // Fallback: drop county context — catches streets that span multiple concelhos
            // or are tagged differently in OSM.
            $fallback = self::nominatimRequest()->get('https://nominatim.openstreetmap.org/search', [
                'street' => $query,
                'state' => 'Madeira',
                'country' => 'pt',
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'limit' => 10,
                'countrycodes' => 'pt',
            ]);

            if ($fallback->successful()) {
                return self::parseNominatimResults($fallback->json(), $query, $dd, $cc);
            }

            return [];

        } catch (\Throwable $e) {
            Log::warning('Nominatim search failed', ['query' => $query, 'error' => $e->getMessage()]);

            return [];
        }
    }
}
