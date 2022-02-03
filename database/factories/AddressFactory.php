<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "country" => "Российская федерация",
            "region" => $this->faker->randomElement(["Республика Татарстан", "г. Москва", "г. Санкт-Петербург", "Нижегородская область", "Кировская область", "Краснодарский край"]),
            "locality" => $this->faker->city,
            "district" => $this->faker->words(1, true),
            "street" => $this->faker->words(1, true),
            "house" => $this->faker->randomDigit(),
            "frame" => $this->faker->randomDigit(),
            "apartment" => $this->faker->randomDigit(),
        ];
    }
}
