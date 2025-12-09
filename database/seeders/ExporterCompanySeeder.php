<?php

namespace Database\Seeders;

use App\Domain\Models\Capability;
use App\Domain\Models\Company;
use App\Domain\Models\Exporter;
use App\Domain\Models\User;
use Illuminate\Database\Seeder;

class ExporterCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $capabilities = Capability::all();

        // Create 10 Exporter Companies
        Company::factory()
            ->count(10)
            ->state(function (array $attributes) {
                return [
                    'type' => 'exporter',
                ];
            })
            ->create()
            ->each(function ($company) use ($capabilities) {
                // Create Exporter profile
                $exporter = Exporter::factory()->create([
                    'company_id' => $company->id,
                ]);

                // Attach random capabilities (3 to 6)
                if ($capabilities->count() > 0) {
                    $exporter->capabilities()->attach(
                        $capabilities->random(rand(3, 6))->pluck('id')->toArray()
                    );
                }
            });
    }
}
