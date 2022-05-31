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
use App\Services\DocumentService;
use App\Services\SecurityService;
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
        $timetable = TimeTable::with("subjectOfProfessor.professor")->get();
        $students = Student::all();
        $services = Service::all();

        for($i = 0; $i < 50; $i++){
            $timetable_rnd = $timetable->random();
            $price = $timetable_rnd->subjectOfProfessor->professor->price;
            $number_of_lessons = $this->faker->numberBetween(1, 3);

            // Сохранение нового заказа
            $order = new Order();
            $order->student_id = $students->random()->id;
            $order->timetable_id = $timetable_rnd->id;
            $order->service_id = $services->random()->id;
            $order->status = "Проверка";
            $order->price = bcdiv($price * (double)$number_of_lessons, 1, 2);

            $currentTime = Carbon::now("Europe/Moscow");
            $order->create_date = $currentTime->toDateTimeString();

            $order->number_of_lessons = $number_of_lessons;

            $order->save();

            // сохранение хэша
            $security = new SecurityService();
            $order->hash = $security->encryptData($order->id);
            $order->update();

            // Создание документа
            $document = new DocumentService();
            $result = $document->getDocument($order->id);
        }
    }
}
