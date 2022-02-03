<?php

namespace Database\Factories;

use App\Models\Speciality;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialityFactory extends Factory
{
    protected $model = Speciality::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "specialty_title" => $this->faker->randomElement([
                "Информационные системы и программирование",
                "Системное администрирование",
                "Программирование в компьютерных системах"
            ]),
            "faculty" => "СПО ИКТЗИ"
        ];
    }
}
