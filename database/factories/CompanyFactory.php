<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = \App\Domain\Models\Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'tax_id' => fake()->bothify('??#########'),
            'website' => fake()->url(),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(['producer', 'exporter', 'logistics', 'both']),
            'logo_path' => null,
            'verification_documents' => [],
            'user_id' => \App\Domain\Models\User::factory(),
        ];
    }
}
