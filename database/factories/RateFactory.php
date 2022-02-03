<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\Rate;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class RateFactory extends Factory
{
    protected $model = Rate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "service_id" => Service::all()->random()->id,
            "position_id" => Position::all()->random()->id,
            "price" => $this->faker->randomFloat(2, 200, 5000)
        ];
    }
}
