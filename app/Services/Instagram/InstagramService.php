<?php

namespace App\Services\Instagram;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Flux Instagram via l'API Graph (compte Business/Creator).
 * Le résultat est mis en cache 24 h pour ne pas dépendre de la latence Meta.
 */
class InstagramService
{
    private const CACHE_KEY = 'instagram.feed';

    /** @return list<array{id:string,media_url:string,permalink:string,caption:?string}> */
    public function feed(int $limit = 6): array
    {
        if (! config('instagram.access_token') || ! config('instagram.business_account_id')) {
            return [];
        }

        return Cache::remember(
            self::CACHE_KEY,
            config('instagram.cache_ttl'),
            fn (): array => $this->fetch($limit),
        );
    }

    /** @return list<array{id:string,media_url:string,permalink:string,caption:?string}> */
    private function fetch(int $limit): array
    {
        try {
            $version = config('instagram.graph_version');
            $accountId = config('instagram.business_account_id');

            $response = Http::acceptJson()
                ->timeout(10)
                ->get("https://graph.facebook.com/{$version}/{$accountId}/media", [
                    'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink',
                    'access_token' => config('instagram.access_token'),
                    'limit' => $limit * 2,
                ])
                ->throw()
                ->json();

            return collect($response['data'] ?? [])
                ->filter(fn (array $item) => in_array($item['media_type'] ?? null, ['IMAGE', 'CAROUSEL_ALBUM'], true))
                ->map(fn (array $item) => [
                    'id' => (string) $item['id'],
                    'media_url' => $item['media_url'] ?? $item['thumbnail_url'] ?? '',
                    'permalink' => $item['permalink'] ?? '',
                    'caption' => $item['caption'] ?? null,
                ])
                ->filter(fn (array $item) => $item['media_url'] !== '')
                ->take($limit)
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Instagram feed indisponible', ['error' => $e->getMessage()]);

            return [];
        }
    }
}
