<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "id" => User::factory()->create()->id,
            "group_id" => Group::all()->random()->id,
            "receipt_date" => $this->faker->date(),
        ];
    }
}
