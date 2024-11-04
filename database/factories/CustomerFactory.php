<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
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
        $fullName = explode(" ", $this->faker->name());
        return [
            'name' => reset($fullName),
            'surname' => end($fullName),
            'balance' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
