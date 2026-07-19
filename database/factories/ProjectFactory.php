<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Dự án NOXH '.fake()->unique()->company();
        $priceFrom = fake()->numberBetween(12, 18) * 1_000_000;

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'province_id' => Province::factory(),
            'investor_id' => Investor::factory(),
            'district' => fake()->citySuffix(),
            'address' => fake()->streetAddress(),
            'former_address' => fake()->optional()->streetAddress(),
            'status' => fake()->randomElement(ProjectStatus::cases()),
            'application_start_at' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'application_end_at' => fake()->dateTimeBetween('+1 month', '+3 months'),
            'total_units' => fake()->numberBetween(100, 2000),
            'price_from' => $priceFrom,
            'price_to' => $priceFrom + fake()->numberBetween(2, 6) * 1_000_000,
            'area_from' => fake()->numberBetween(30, 45),
            'area_to' => fake()->numberBetween(50, 75),
            'description' => fake()->paragraphs(3, true),
            'application_guide' => fake()->paragraph(),
            'source_name' => 'Nhập tay',
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['published_at' => null]);
    }
}
