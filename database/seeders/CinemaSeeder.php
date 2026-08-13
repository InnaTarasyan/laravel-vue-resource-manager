<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Film;
use App\Models\Showtime;
use App\Models\Theater;
use App\Services\KinoIsApi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

class CinemaSeeder extends Seeder
{
    /**
     * Countries that expose theater directories in the kino.is API.
     *
     * @var list<string>
     */
    private array $theaterCountries = ['UA', 'KZ'];

    /**
     * How many theater detail pages to expand into showtimes (keeps seed fast).
     */
    private int $theaterDetailLimit = 10;

    public function run(): void
    {
        $api = new KinoIsApi(lang: 'en');

        $this->command?->info('Fetching countries from kino.is…');
        $countries = $this->seedCountries($api);

        $this->command?->info('Fetching cities…');
        $citiesByCountry = $this->seedCities($api, $countries);

        $this->command?->info('Fetching theaters…');
        $theaters = $this->seedTheaters($api, $countries, $citiesByCountry);

        $this->command?->info('Fetching now-showing films…');
        $this->seedNowShowingFilms($api);

        $this->command?->info("Fetching showtimes for top {$this->theaterDetailLimit} theaters…");
        $this->seedShowtimes($api, $theaters);

        $this->command?->table(
            ['Table', 'Rows'],
            [
                ['countries', Country::count()],
                ['cities', City::count()],
                ['theaters', Theater::count()],
                ['films', Film::count()],
                ['showtimes', Showtime::count()],
            ],
        );
    }

    /**
     * @return array<string, Country>
     */
    private function seedCountries(KinoIsApi $api): array
    {
        $map = [];

        foreach ($api->countries() as $row) {
            $country = Country::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'provider' => $row['provider'] ?? null,
                    'source' => $row['source'] ?? null,
                    'has_sessions' => (bool) ($row['sessions'] ?? false),
                    'has_cities' => (bool) ($row['cities'] ?? false),
                    'has_theaters' => (bool) ($row['theaters'] ?? false),
                ],
            );

            $map[$country->code] = $country;
        }

        return $map;
    }

    /**
     * @param  array<string, Country>  $countries
     * @return array<string, array<string, City>>
     */
    private function seedCities(KinoIsApi $api, array $countries): array
    {
        $byCountry = [];

        foreach ($this->theaterCountries as $code) {
            if (! isset($countries[$code])) {
                continue;
            }

            $byCountry[$code] = [];

            foreach ($api->cities($code) as $row) {
                $city = City::query()->updateOrCreate(
                    [
                        'country_id' => $countries[$code]->id,
                        'code' => $row['code'],
                    ],
                    ['name' => $row['name']],
                );

                $byCountry[$code][$city->code] = $city;
                $byCountry[$code][mb_strtolower($city->name)] = $city;
            }
        }

        return $byCountry;
    }

    /**
     * @param  array<string, Country>  $countries
     * @param  array<string, array<string, City>>  $citiesByCountry
     * @return list<Theater>
     */
    private function seedTheaters(KinoIsApi $api, array $countries, array $citiesByCountry): array
    {
        $theaters = [];

        foreach ($this->theaterCountries as $code) {
            if (! isset($countries[$code])) {
                continue;
            }

            $country = $countries[$code];
            $cityLookup = $citiesByCountry[$code] ?? [];

            if ($code === 'KZ') {
                $uniqueCities = [];
                foreach ($cityLookup as $key => $city) {
                    if ($key === $city->code) {
                        $uniqueCities[$city->code] = $city;
                    }
                }

                foreach ($uniqueCities as $city) {
                    foreach ($api->theaters($code, $city->code) as $row) {
                        $theaters[] = $this->upsertTheater($country, $city, $row);
                    }
                }
            } else {
                foreach ($api->theaters($code) as $row) {
                    $city = $this->matchCity($cityLookup, $row['city'] ?? null);
                    $theaters[] = $this->upsertTheater($country, $city, $row);
                }
            }
        }

        return $theaters;
    }

    /**
     * @param  array<string, City>  $cityLookup
     */
    private function matchCity(array $cityLookup, ?string $cityName): ?City
    {
        if ($cityName === null || $cityName === '') {
            return null;
        }

        return $cityLookup[mb_strtolower($cityName)] ?? null;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function upsertTheater(Country $country, ?City $city, array $row): Theater
    {
        return Theater::query()->updateOrCreate(
            ['slug' => $row['slug']],
            [
                'country_id' => $country->id,
                'city_id' => $city?->id,
                'name' => $row['name'],
                'city_name' => $row['city'] ?? $city?->name,
                'address' => $row['address'] ?? null,
                'film_count' => (int) ($row['filmCount'] ?? 0),
                'session_count' => (int) ($row['sessionCount'] ?? 0),
            ],
        );
    }

    private function seedNowShowingFilms(KinoIsApi $api): void
    {
        foreach ($this->theaterCountries as $code) {
            foreach ($api->nowShowing($code) as $row) {
                $this->upsertFilm($row);
            }
        }
    }

    /**
     * @param  list<Theater>  $theaters
     */
    private function seedShowtimes(KinoIsApi $api, array $theaters): void
    {
        usort($theaters, fn (Theater $a, Theater $b) => $b->session_count <=> $a->session_count);

        $selected = array_slice($theaters, 0, $this->theaterDetailLimit);

        foreach ($selected as $index => $theater) {
            $this->command?->info(sprintf(
                '  [%d/%d] %s (%s)',
                $index + 1,
                count($selected),
                $theater->name,
                $theater->slug,
            ));

            try {
                $detail = $api->theater($theater->slug, $theater->country->code);
            } catch (Throwable $e) {
                $this->command?->warn("  skipped: {$e->getMessage()}");

                continue;
            }

            if ($detail === null) {
                continue;
            }

            DB::transaction(function () use ($theater, $detail) {
                Showtime::query()->where('theater_id', $theater->id)->delete();

                foreach ($detail['films'] ?? [] as $filmRow) {
                    $film = $this->upsertFilm($filmRow);

                    foreach ($filmRow['days'] ?? [] as $day) {
                        foreach ($day['sessions'] ?? [] as $session) {
                            Showtime::query()->create([
                                'theater_id' => $theater->id,
                                'film_id' => $film->id,
                                'date' => $day['date'],
                                'time' => $session['time'],
                                'tech' => $session['tech'] ?? null,
                                'price' => $session['price'] ?? null,
                                'currency' => $session['currency'] ?? null,
                                'booking_url' => $session['bookingUrl'] ?? null,
                            ]);
                        }
                    }
                }
            });
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function upsertFilm(array $row): Film
    {
        return Film::query()->updateOrCreate(
            ['external_id' => $row['filmId']],
            [
                'title' => $row['title'],
                'year' => $row['year'] ?? null,
                'poster' => $row['poster'] ?? null,
                'url' => $row['url'] ?? null,
                'rating' => $row['rating'] ?? null,
                'cinema_count' => $row['cinemaCount'] ?? null,
                'session_count' => $row['sessionCount'] ?? null,
            ],
        );
    }
}
