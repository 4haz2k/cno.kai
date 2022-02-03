<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Group;
use App\Models\Order;
use App\Models\Position;
use App\Models\Professor;
use App\Models\Rate;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectsOfProfessor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Address::factory(90)->create();
        Speciality::factory(90)->create();
        Group::factory(60)->create();

        Service::factory(90)->create();
        Position::factory(90)->create();
        Rate::factory(60)->create();

        Student::factory(75)->create();
        Professor::factory(75)->create();

        Subject::factory(90)->create();
        SubjectsOfProfessor::factory(60)->create();

        Order::factory(90)->create();
    }
}
