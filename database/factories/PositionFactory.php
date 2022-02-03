<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "title" => $this->faker->randomElement(["Заведующий кафедры", "Преподаватель", "Лаборант"]),
            "rank" => $this->faker->randomElement(["Аспирант", "Профессор", "Доцент", "Кандидат наук"]),
            "description" => "здесь типо описание, но я просто напишу, что дельфины это круто"
        ];
    }
}
