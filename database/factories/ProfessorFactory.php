<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\Professor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessorFactory extends Factory
{
    protected $model = Professor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "id" => User::factory()->create()->id,
            "position_id" => Position::all()->random()->id,
            "personnel_number" => $this->faker->numberBetween(1, 10000),
            "ITN" => $this->faker->numberBetween(7000000000, 8000000000),
            "INILA" => $this->faker->numberBetween(10000000000, 99999999999),
            "department" => $this->faker->randomElement(["ИКТЗИ", "ИРЭТ", "ИАЭНТ"]),
            "date_of_commencement_of_teaching_activity" => $this->faker->date(),
            "description" => "я типо дельфин"
        ];
    }
}
