<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public static function findCached(string $query, string $dd, string $cc): ?self
    {
        $hash = self::buildHash($query, $dd, $cc);
        $entry = self::where('search_hash', $hash)->first();

        if ($entry) {
            $entry->increment('hit_count');
            $entry->update(['last_used_at' => now()]);
        }

        return $entry;
    }

    public static function store(array $data): self
    {
        return self::create(array_merge($data, [
            'hit_count' => 1,
            'last_used_at' => now(),
        ]));
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
}
