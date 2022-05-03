<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Service;
use App\Models\Student;
use App\Models\TimeTable;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "student_id" => Student::all()->random()->id,
            "timetable_id" => TimeTable::factory()->create()->id,
            "service_id" => Service::all()->random()->id,
            "status" => $this->faker->randomElement(["Ожидает исполнения", "Исполнено"]),
            "price" => $this->faker->randomFloat(2, 200, 5000),
            "create_date" => $this->faker->dateTime,
            "number_of_lessons" => $this->faker->numberBetween(1, 10)
        ];
    }
}
