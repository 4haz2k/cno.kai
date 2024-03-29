<?php

namespace Database\Factories;

use App\Models\SubjectsOfProfessor;
use App\Models\TimeTable;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeTableFactory extends Factory
{
    protected $model = TimeTable::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "subject_of_professor_id" => SubjectsOfProfessor::all()->random()->id,
            "date" => $this->faker->dateTimeBetween("now", "+6 months", "Europe/Moscow"),
            "classroom" => $this->faker->numberBetween(100, 999),
            "building" => $this->faker->randomElement(["7", "5", "2", "1", "3", "8"])
        ];
    }
}
