<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "title" => $this->faker->randomElement(["Дополнительное занятие", "Сдача долгов", "Репетиторство"]),
            "description" => "Типо описание, типо дельфин, все дела..."
        ];
    }
}
