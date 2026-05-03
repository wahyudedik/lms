<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'is_active' => true,
            'show_landing_page' => true,
            'is_landing_active' => false,
            'hero_title' => fake()->sentence(5),
            'hero_subtitle' => fake()->sentence(8),
            'hero_description' => fake()->paragraph(),
            'hero_cta_text' => 'Mulai Sekarang',
            'hero_cta_link' => '/login',
            'contact_address' => fake()->address(),
            'contact_phone' => fake()->phoneNumber(),
            'contact_email' => fake()->companyEmail(),
            'meta_title' => $name . ' - LMS',
            'meta_description' => fake()->sentence(15),
        ];
    }

    /**
     * State: school is active for landing page
     */
    public function landingActive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_landing_active' => true,
        ]);
    }

    /**
     * State: school is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
