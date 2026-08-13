<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KinoIsApi
{
    public function __construct(
        private readonly string $baseUrl = 'https://kino.is',
        private readonly string $lang = 'en',
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function countries(): array
    {
        return $this->get('/api/v1/cinema/countries')['countries'] ?? [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function cities(string $countryCode): array
    {
        return $this->get('/api/v1/cinema/cities', ['country' => $countryCode])['cities'] ?? [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function theaters(string $countryCode, ?string $cityCode = null): array
    {
        $query = ['country' => $countryCode];

        if ($cityCode !== null) {
            $query['city'] = $cityCode;
        }

        return $this->get('/api/v1/cinema/theaters', $query)['theaters'] ?? [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function theater(string $slug, string $countryCode, ?string $cityCode = null): ?array
    {
        $query = ['country' => $countryCode];

        if ($cityCode !== null) {
            $query['city'] = $cityCode;
        }

        return $this->get("/api/v1/cinema/theaters/{$slug}", $query)['theater'] ?? null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function nowShowing(string $countryCode): array
    {
        return $this->get('/api/v1/cinema/now-showing', ['country' => $countryCode])['films'] ?? [];
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function get(string $path, array $query = []): array
    {
        $query['lang'] = $this->lang;

        /** @var array<string, mixed> $payload */
        $payload = Http::timeout(30)
            ->acceptJson()
            ->retry(2, 500)
            ->get($this->baseUrl.$path, $query)
            ->throw()
            ->json();

        return $payload;
    }
}
