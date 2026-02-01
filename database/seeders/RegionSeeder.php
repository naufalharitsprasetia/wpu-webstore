<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Fetching Provinces');

        $provinces = $this->fetch('https://wilayah.id/api/provinces.json');

        foreach ($provinces as $province) {
            Region::firstOrCreate(
                ['code' => $province['code']],
                [
                    'name' => $province['name'],
                    'type' => 'province',
                    'parent_code' => null,
                ]
            );

            $this->command->info("Fetching Regencies for {$province['name']}");

            $regencies = $this->fetch("https://wilayah.id/api/regencies/{$province['code']}.json");

            foreach ($regencies as $regency) {
                Region::firstOrCreate(
                    ['code' => $regency['code']],
                    [
                        'name' => $regency['name'],
                        'type' => 'regency',
                        'parent_code' => $province['code'],
                    ]
                );

                $this->command->info("Fetching Districts for {$regency['name']}");

                $districts = $this->fetch("https://wilayah.id/api/districts/{$regency['code']}.json");

                foreach ($districts as $district) {
                    Region::firstOrCreate(
                        ['code' => $district['code']],
                        [
                            'name' => $district['name'],
                            'type' => 'district',
                            'parent_code' => $regency['code'],
                        ]
                    );

                    $this->command->info("Fetching Villages for {$district['name']}");

                    $villages = $this->fetch("https://wilayah.id/api/villages/{$district['code']}.json");

                    foreach ($villages as $village) {
                        Region::firstOrCreate(
                            ['code' => $village['code']],
                            [
                                'name' => $village['name'],
                                'type' => 'village',
                                'parent_code' => $district['code'],
                                'postal_code' => $village['postal_code'] ?? null,
                            ]
                        );
                    }
                }
            }
        }
    }

    private function fetch(string $url): array
    {
        try {
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->get($url);

            sleep(1); // ⬅️ PENTING: cegah rate limit

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }

            $this->command->error("Failed request: {$url} ({$response->status()})");
        } catch (\Exception $e) {
            $this->command->error("Connection error: {$url}");
        }

        return [];
    }
}