<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pharmacy>
 */
class PharmacyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a unique license number
        $licenseNumber = 'PH-' . strtoupper(Str::random(6)) . '-' . rand(100, 999);

        return [
            'name' => $this->faker->company() . ' Pharmacy',
            'license_number' => $licenseNumber,
            'country' => $this->faker->country(),
            'region' => $this->faker->state(),
            'district' => $this->faker->citySuffix() . ' District',
            'location' => $this->faker->streetAddress(),
            'working_hours' => 'Mon-Fri: 8:00 AM - 10:00 PM, Sat-Sun: 9:00 AM - 8:00 PM',
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->companyEmail(),
            'license_expiry' => $this->faker->dateTimeBetween('+1 year', '+5 years'),
            'pharmacy_logo' => 'default_pharmacy_logo.png', // Default will be used anyway
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => now(),
        ];
    }
}