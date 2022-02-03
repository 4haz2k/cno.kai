<?php

namespace Database\Factories;

use App\Models\Professor;
use App\Models\Subject;
use App\Models\SubjectsOfProfessor;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectsOfProfessorFactory extends Factory
{
    protected $model = SubjectsOfProfessor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "subject_id" => Subject::all()->random()->id,
            "professor_id" => Professor::all()->random()->id
        ];
    }
}
