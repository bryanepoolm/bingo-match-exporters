<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Models\Exporter>
 */
class ExporterFactory extends Factory
{
    protected $model = \App\Domain\Models\Exporter::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Domain\Models\Company::factory(),
        ];
    }
}
