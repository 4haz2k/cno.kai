<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Speciality;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "group_code" => $this->faker->randomElement([4433, 4421, 5510, 2142, 4432, 4455]),
            "specialty_id" => Speciality::all()->random()->id
        ];
    }
}
