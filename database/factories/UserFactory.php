<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Passport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "id" => Passport::factory()->create()->id,
            "actual_place_of_residence_id" => Address::all()->random()->id,
            "login" => $this->faker->email,
            "password" => Hash::make("password"),
            "phone" => $this->faker->phoneNumber,
            "role" => "user"
        ];
    }
}
