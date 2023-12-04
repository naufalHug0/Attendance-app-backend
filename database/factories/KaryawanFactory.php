<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Karyawan>
 */
class KaryawanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'email' => fake()->unique()->safeEmail(),
            'password'=> Hash::make('testing'),
            'alamat' => 'Jl. Mampang Prapatan Raya No.108 Jakarta Selatan 12760',
            'jabatan' => fake()->jobTitle(),
            'profile_image' => [
                'http://enkripa.test/storage/profile-images/profile_1.png',
                'http://enkripa.test/storage/profile-images/profile_2.jpeg',
                'http://enkripa.test/storage/profile-images/profile_3.png',
                'http://enkripa.test/storage/profile-images/profile_4.png',
                'http://enkripa.test/storage/assets/Enkripa_Logo_red.png',
            ][mt_rand(0,4)],
            'role' => 'karyawan',
            'total_cuti_tahunan'=>12
        ];
    }
}
