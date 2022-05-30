<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Group;
use App\Models\Order;
use App\Models\Passport;
use App\Models\Position;
use App\Models\Professor;
use App\Models\Rate;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectsOfProfessor;
use App\Models\TimeTable;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return Generator
     * @throws BindingResolutionException
     */
    protected function withFaker(): Generator
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $subjects = [
            [
                "title" => "Управление проектами",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Основы программирования",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Основы экономики",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Прикладная информатика",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Прикладное програмирование",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Математический анализ",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Высшая математика",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Разработка программных модулей",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Внедрение и поддержка компьютерных систем",
                "description" => "Это поле описания предмета"
            ],
            [
                "title" => "Введение в предметную область",
                "description" => "Это поле описания предмета"
            ],
        ];

        foreach ($subjects as $subject) {
            $subject_entity = new Subject();
            $subject_entity->title = $subject["title"];
            $subject_entity->description = $subject["description"];
            $subject_entity->save();
        }
    }
}
