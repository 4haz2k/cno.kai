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
        for($i = 2; $i <= 9; $i++){
            $address = new Address();
            $address->country = "Российская федерация";
            $address->region = "Республика Татарстан";
            $address->locality = "Казань";
            $address->district = $this->faker->randomElement(["Вахитовский", "Авиастроительный", "Кировский", "Московски", "Ново-Савиновский", "Советский", "Приволжский"]);
            $address->street = "Восстания";
            $address->house = $this->faker->numberBetween(1, 15);
            $address->frame = $this->faker->numberBetween(1, 15);
            $address->apartment = $this->faker->numberBetween(1, 15);
            $address->save();

            $passport = new Passport();
            $passport->id = $address->id;
            $passport->series = $this->faker->numberBetween(9200, 9999);
            $passport->number = $this->faker->numberBetween(800000, 899999);
            $passport->date_of_issue = $this->faker->dateTimeBetween(
                Carbon::createFromDate(2014, 1, 1)->format("Y-m-d"),
                Carbon::createFromDate(2016, 12, 31)->format("Y-m-d"),
            );
            $passport->issued = "Отделом УФМС по городу Казани";
            $passport->division_code = $this->faker->numberBetween(100, 999)."-".$this->faker->numberBetween(100, 999);
            $passport->place_of_residence_id = $address->id;
            $passport->secondname = $this->faker->lastName;
            $passport->firstname = $this->faker->firstName("M");
            $passport->thirdname = $this->faker->lastName;
            $passport->ITN = $this->faker->numberBetween(111111111111, 888888888888);
            $passport->INILA = $this->faker->numberBetween(11111111111, 88888888888);
            $passport->birthday = $this->faker->dateTimeBetween(
                Carbon::createFromDate(2002, 1, 1)->format("Y-m-d"),
                Carbon::createFromDate(2005, 12, 31)->format("Y-m-d"),
            );
            $passport->sex = "M";
            $passport->save();

            $user = new User();
            $user->id = $passport->id;
            $user->actual_place_of_residence_id = $address->id;
            $user->login = "user0".$i."@mail.ru";
            $user->password = Hash::make("User0".$i."!");
            $user->phone = $this->faker->phoneNumber;
            $user->role = "STUDENT";
            $user->save();

            $group = Group::all()->random();

            $student = new Student();
            $student->id = $user->id;
            $student->group_id = $group->id;
            $student->receipt_date = $this->faker->dateTimeBetween(
                Carbon::createFromDate(2017, 9, 1)->format("Y-m-d"),
                Carbon::createFromDate(2019, 9, 1)->format("Y-m-d"),
            );
            $student->save();
        }
    }
}
