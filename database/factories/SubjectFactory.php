<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "title" => $this->faker->randomElement(["Высшая математика", "Линейная алгебра", "Основы программирования", "Прикладное программирвоание"]),
            "description" => "Описание таково, что я дельфин епт"
        ];
    }
}
