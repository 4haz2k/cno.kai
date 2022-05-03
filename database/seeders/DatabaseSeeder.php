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
        // Адреса и паспорт
        Address::factory(60)->create();
        Passport::factory(60)->create();

        // создание пользователей с ролями
        $user_students = [];
        $user_professors = [];
        $user_admins = [];

        // id: 1-20 - students; 21-40 - professors; 41-60 - admins;

        // студенты

        for($i = 1; $i <= 20; $i++){
            array_push($user_students,
                [
                    "id" => $i,
                    "actual_place_of_residence_id" => Address::all()->random()->id,
                    "login" => $this->faker->email,
                    "password" => Hash::make("password"),
                    "phone" => $this->faker->phoneNumber,
                    "role" => "STUDENT"
                ]);
        }

        // преподаватели
        for($i = 21; $i <= 40; $i++){
            array_push($user_professors,
                [
                    "id" => $i,
                    "actual_place_of_residence_id" => Address::all()->random()->id,
                    "login" => $this->faker->email,
                    "password" => Hash::make("password"),
                    "phone" => $this->faker->phoneNumber,
                    "role" => "PREPOD"
                ]);
        }

        // админы
        for($i = 41; $i <= 60; $i++){
            array_push($user_admins,
                [
                    "id" => $i,
                    "actual_place_of_residence_id" => Address::all()->random()->id,
                    "login" => $this->faker->email,
                    "password" => Hash::make("password"),
                    "phone" => $this->faker->phoneNumber,
                    "role" => "ADMIN"
                ]);
        }

        // добавление
        User::insert($user_students);
        User::insert($user_professors);
        User::insert($user_admins);

        // специальности
        $specialities = [
            [
              "specialty_title" => "Информационные системы и программирование",
              "faculty" => "СПО ИКТЗИ"
            ],
            [
                "specialty_title" => "Системное администрирование",
                "faculty" => "СПО ИКТЗИ"
            ],
            [
                "specialty_title" => "Программирование в компьютерных системах",
                "faculty" => "СПО ИКТЗИ"
            ],
            [
                "specialty_title" => "Защита информации",
                "faculty" => "ИКТЗИ"
            ],
            [
                "specialty_title" => "Прикладное программирование",
                "faculty" => "ИКТЗИ"
            ],
            [
                "specialty_title" => "Прикладная математика и информатика",
                "faculty" => "ИКТЗИ"
            ],
            [
                "specialty_title" => "Техническая физика",
                "faculty" => "ФИЗМАТ"
            ],
            [
                "specialty_title" => "Наноинженерия",
                "faculty" => "ФИЗМАТ"
            ],
            [
                "specialty_title" => "Лазерная техника и лазерные технологии",
                "faculty" => "ФИЗМАТ"
            ],
            [
                "specialty_title" => "Управление персоналом",
                "faculty" => "ИИЭП"
            ],
        ];

        Speciality::insert($specialities);

        // группы
        $groups = [
            [
                "group_code" => "4433",
                "specialty_id" => 1
            ],
            [
                "group_code" => "4433",
                "specialty_id" => 1
            ],
            [
                "group_code" => "4432",
                "specialty_id" => 1
            ],
            [
                "group_code" => "4432",
                "specialty_id" => 1
            ],
            [
                "group_code" => "4441",
                "specialty_id" => 3
            ],
            [
                "group_code" => "4441",
                "specialty_id" => 3
            ],
            [
                "group_code" => "5102",
                "specialty_id" => 2
            ],
            [
                "group_code" => "5103",
                "specialty_id" => 4
            ],
            [
                "group_code" => "5104",
                "specialty_id" => 5
            ],
            [
                "group_code" => "1235",
                "specialty_id" => 6
            ],
            [
                "group_code" => "5212",
                "specialty_id" => 7
            ],
            [
                "group_code" => "5215",
                "specialty_id" => 8
            ],
            [
                "group_code" => "3212",
                "specialty_id" => 9
            ],
            [
                "group_code" => "8412",
                "specialty_id" => 10
            ]
        ];

        Group::insert($groups);

        // студенты(таблица)
        $students = [];

        for ($i = 1; $i <= 20; $i++){
            array_push($students, [
                "id" => $i,
                "group_id" => Group::all()->random()->id,
                "receipt_date" => $this->faker->date(),
            ]);
        }

        Student::insert($students);

        // Услуги
        $services = [
            [
                "title" => "Дополнительное занятие",
                "description" => "Это поле описания, тестовая часть"
            ],
            [
                "title" => "Сдача экзамена",
                "description" => "Это поле описания, тестовая часть"
            ],
            [
                "title" => "Сдача лабораторных работ",
                "description" => "Это поле описания, тестовая часть"
            ],
            [
                "title" => "Сдача долгов",
                "description" => "Это поле описания, тестовая часть"
            ],
            [
                "title" => "Сдача зачёта",
                "description" => "Это поле описания, тестовая часть"
            ],
        ];
        Service::insert($services);

        // преподаватели(таблица)
        $professors = [];

        for($i = 21; $i <= 40; $i++){
            array_push($professors, [
                "id" => $i,
                "position" => $this->faker->randomElement(["Заведующий кафедры", "Преподаватель", "Лаборант"]),
                "personal_number" => $this->faker->numberBetween(1, 10000),
                "department" => $this->faker->randomElement(["ИКТЗИ", "СПО ИКТЗИ", "ИИЭП", "ФИЗМАТ"]),
                "date_of_commencement_of_teaching_activity" => $this->faker->date(),
                "description" => "Это поле с описанием преподавателя",
                "price" => $this->faker->randomFloat(2, 200, 5000)
            ]);
        }

        Professor::insert($professors);

        // предметы
        $subjects = [
            [
                "title" => "Управление проектами",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Основы программирования",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Основы экономики",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Прикладная информатика",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Прикладное програмирование",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Математический анализ",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Высшая математика",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Разработка программных модулей",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Внедрение и поддержка компьютерных систем",
                "description" => "Это поле описания предмета."
            ],
            [
                "title" => "Введение в предметную область",
                "description" => "Это поле описания предмета."
            ],
        ];

        Subject::insert($subjects);

        // предметы преподавателей
        SubjectsOfProfessor::factory(30)->create();

        TimeTable::factory(400)->create();

        // заказы
        //Order::factory(50)->create();


//        Address::factory(90)->create();
//        Speciality::factory(90)->create();
//        Group::factory(60)->create();
//
//        Service::factory(90)->create();
//        Position::factory(90)->create();
//        Rate::factory(60)->create();
//
//        Student::factory(75)->create();
//        Professor::factory(75)->create();
//
//        Subject::factory(90)->create();
//        SubjectsOfProfessor::factory(60)->create();
//
//        Order::factory(90)->create();
    }
}
