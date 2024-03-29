<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Passport;
use Illuminate\Database\Eloquent\Factories\Factory;
use function Symfony\Component\Translation\t;

class PassportFactory extends Factory
{
    protected $model = Passport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "series" => $this->faker->numberBetween(1000, 9999),
            "number" => $this->faker->numberBetween(100000, 999999),
            "date_of_issue" => $this->faker->date(),
            "issued" => $this->faker->words(10, true),
            "division_code" => $this->faker->numberBetween(1000, 9999),
            "place_of_residence_id" => Address::all()->random()->id,
            "secondname" => $this->faker->lastName,
            "firstname" => $this->faker->firstName,
            "thirdname" => $this->faker->lastName,
            "birthday" => $this->faker->date(),
            "sex" => $this->faker->randomElement(["M", "W"]),
            "ITN" => $this->faker->numberBetween(7000000000, 8000000000),
            "INILA" => $this->faker->numberBetween(10000000000, 99999999999),
        ];
    }
}
