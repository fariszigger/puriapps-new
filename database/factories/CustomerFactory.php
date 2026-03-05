<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => fake()->randomElement(['Perorangan', 'Badan']),
            'identity_number' => fake()->unique()->numerify('################'), // 16 digits NIK
            'phone_number' => fake()->phoneNumber(),
            'pob' => fake()->city(),
            'dob' => fake()->date(),
            'gender' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'marital_status' => fake()->randomElement(['Menikah', 'Belum Menikah', 'Janda/Duda']),
            'mother_name' => fake()->name('female'),
            'education' => fake()->randomElement(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2']),
            'emergency_contact' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'village' => fake()->streetName(),
            'district' => fake()->citySuffix(),
            'regency' => fake()->city(),
            'province' => fake()->state(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'relation' => fake()->randomElement(['Nasabah Baru', 'Nasabah Lama']),
            'last_financing_ceiling' => fake()->numberBetween(1000000, 100000000),
            'credit_quality' => fake()->randomElement(['Lancar', 'Dalam Perhatian Khusus', 'Kurang Lancar', 'Diragukan', 'Macet']),
            'description' => fake()->sentence(),
        ];
    }
}
