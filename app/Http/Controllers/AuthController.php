<?php

namespace App\Http\Controllers;

use App\Enum\RolesEnum;
use App\Http\Requests\ProfileEditRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\Address;
use App\Models\Passport;
use App\Models\Professor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Авторизация по ролям
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $user = User::with(["passport.placeOfResidence", "student.group.speciality", "professor"])->where("login", \request("email"))->first();

        if(!$user)
            return response()->json(['field' => 'email', 'error' => 'Данного пользователя не существует'], 401);

        if (!Hash::check(\request("password"), $user->password))
            return response()->json(['field' => 'password', 'error' => 'Введённый пароль неверный. Повторите попытку.'], 401);

        $passport = $user->passport;

        switch ($user->role){
            case RolesEnum::student:
                $data = [
                    "id" => $user->id,
                    "passport" => $passport,
                    "faculty" => $user->student->group->speciality->faculty,
                    "email" => $user->login,
                    "telephone" => $user->phone,
                    "group" => $user->student->group->group_code,
                    "img" => $user->img,
                    "role" => $user->role
                ];
                break;
            case RolesEnum::professor:
                $data = [
                    "id" => $user->id,
                    "passport" => $passport,
                    "email" => $user->login,
                    "telephone" => $user->phone,

                    "price" => $user->professor->price,
                    "exp" => $user->professor->date_of_commencement_of_teaching_activity,
                    "personal_number" => $user->professor->personal_number,

                    "img" => $user->img,
                    "role" => $user->role,
                    "description" => $user->professor->description
                ];
                break;

            case RolesEnum::admin:
                $data = [
                    "id" => $user->id,
                    "passport" => $passport,
                    "email" => $user->login,
                    "telephone" => $user->phone,
                    "img" => $user->img,
                    "role" => $user->role
                ];
                break;

            default:
                $data = ["error" => "Undefined role: {$user->role}"];
                break;
        }

        return response()->json($data);
    }

    /**
     * Регистрация пользователей
     * @param RegistrationRequest $request
     * @return JsonResponse
     */
    public function registration(RegistrationRequest $request): JsonResponse
    {
        // Регистрируем новый адрес
        $address = new Address();
        $address->country = \request("passport.place_of_residence.country");
        $address->region = \request("passport.place_of_residence.region");
        $address->locality = \request("passport.place_of_residence.locality");
        $address->district = \request("passport.place_of_residence.district");
        $address->street = \request("passport.place_of_residence.street");
        $address->house = \request("passport.place_of_residence.house");
        $address->frame = \request("passport.place_of_residence.frame");
        $address->apartment = \request("passport.place_of_residence.apartment");
        $address->save();

        // Если не проживает по месту прописки
        if($request->the_same_address == false){
            $address_to_user = new Address();
            $address_to_user->country = \request("place_of_residence.country");
            $address_to_user->region = \request("place_of_residence.region");
            $address_to_user->locality = \request("place_of_residence.locality");
            $address_to_user->district = \request("place_of_residence.district");
            $address_to_user->street = \request("place_of_residence.street");
            $address_to_user->house = \request("place_of_residence.house");
            $address_to_user->frame = \request("place_of_residence.frame");
            $address_to_user->apartment = \request("place_of_residence.apartment");
            $address_to_user->save();
        }

        // регистрируем новый паспорт
        $passport = new Passport();
        $passport->series = \request("passport.serial");
        $passport->number = \request("passport.number");
        $passport->date_of_issue = date('Y-m-d', strtotime(\request("passport.date_of_issue")));
        $passport->issued = \request("passport.issued_by");
        $passport->division_code = \request("passport.department_code");
        $passport->ITN = \request("passport.INN");
        $passport->INILA = \request("passport.SNILS");
        $passport->place_of_residence_id = $address->id;
        $passport->secondname = \request("passport.surname");
        $passport->firstname = \request("passport.name");
        $passport->thirdname = \request("passport.patronymic");
        $passport->birthday = date('Y-m-d', strtotime(\request("passport.date_of_birth")));
        $passport->sex = \request("passport.sex");
        $passport->save();

        // регистрируем нового пользователя
        $user = new User();
        $user->id = $passport->id;
        $user->login = \request("email");
        $user->password = Hash::make(\request("password"));
        $user->phone = \request("telephone");
        $user->role = \request("role");

        if($request->the_same_address == false){
            $user->actual_place_of_residence_id = $address_to_user->id;
        }
        else{
            $user->actual_place_of_residence_id = $address->id;
        }

        $user->save();

        // Если роль препода
        if($request->role == RolesEnum::professor){
            $professor = new Professor();
            $professor->id = $user->id;
            $professor->position = \request("position");
            $professor->personal_number = \request("personal_number");
            $professor->department = \request("faculty");
            $professor->date_of_commencement_of_teaching_activity = date('Y-m-d', strtotime(\request("exp")));
            $professor->price = \request("price");
            $professor->save();
        }

        // Если роль студента
        if($request->role == RolesEnum::student){
            $student = new Student();
            $student->id = $user->id;
            $student->group_id = \request("group_id");
            $student->receipt_date = date('Y-m-d', strtotime(\request("receipt_date")));
            $student->save();
        }

        // регистрация
        return response()->json([
            'message' => 'Successfully registration!',
            'data' => [
                "id" => $user->id,
                "name" => $passport->firstname,
                "surname" => $passport->secondname,
                "role" => $user->role
            ]
        ]);
    }

    /**
     * Изменение пользователей
     * @param ProfileEditRequest $request
     * @return JsonResponse
     */
    public function profileEdit(ProfileEditRequest $request): JsonResponse
    {
        // Обновляем адрес
        $address = Address::where("id", $request->user_id)->first();
        $address->country = \request("passport.place_of_residence.country");
        $address->region = \request("passport.place_of_residence.region");
        $address->locality = \request("passport.place_of_residence.locality");
        $address->district = \request("passport.place_of_residence.district");
        $address->street = \request("passport.place_of_residence.street");
        $address->house = \request("passport.place_of_residence.house");
        $address->frame = \request("passport.place_of_residence.frame");
        $address->apartment = \request("passport.place_of_residence.apartment");
        $address->save();

        // Если не проживает по месту прописки
        if($request->the_same_address == false){
            $address_to_user = Address::where("id", $request->place_of_residence_id)->first();
            $address_to_user->country = \request("place_of_residence.country");
            $address_to_user->region = \request("place_of_residence.region");
            $address_to_user->locality = \request("place_of_residence.locality");
            $address_to_user->district = \request("place_of_residence.district");
            $address_to_user->street = \request("place_of_residence.street");
            $address_to_user->house = \request("place_of_residence.house");
            $address_to_user->frame = \request("place_of_residence.frame");
            $address_to_user->apartment = \request("place_of_residence.apartment");
            $address_to_user->save();
        }

        // Обновляем паспорт
        $passport = Passport::where("id", $request->user_id)->first();
        $passport->series = \request("passport.serial");
        $passport->number = \request("passport.number");
        $passport->date_of_issue = date('Y-m-d', strtotime(\request("passport.date_of_issue")));
        $passport->issued = \request("passport.issued_by");
        $passport->division_code = \request("passport.department_code");
        $passport->ITN = \request("passport.INN");
        $passport->INILA = \request("passport.SNILS");
        $passport->place_of_residence_id = $address->id;
        $passport->secondname = \request("passport.surname");
        $passport->firstname = \request("passport.name");
        $passport->thirdname = \request("passport.patronymic");
        $passport->birthday = date('Y-m-d', strtotime(\request("passport.date_of_birth")));
        $passport->save();

        // Обновляем пользователя
        $user = User::where("id", $request->user_id)->first();
        $user->id = $passport->id;
        $user->login = \request("email");
        if(\request("password") && !\request("password") == ""){
            $user->password = Hash::make(\request("password"));
        }
        $user->phone = \request("telephone");
        $user->role = \request("role");

        if($request->the_same_address == false){
            $user->actual_place_of_residence_id = $address_to_user->id;
        }
        else{
            $user->actual_place_of_residence_id = $address->id;
        }

        $user->save();

        // Если роль препода
        if($request->role == RolesEnum::professor){
            $professor = Professor::where("id", $request->user_id)->first();
            $professor->id = $user->id;
            $professor->position = \request("position");
            $professor->personal_number = \request("personal_number");
            $professor->department = \request("faculty");
            $professor->date_of_commencement_of_teaching_activity = date('Y-m-d', strtotime(\request("exp")));
            $professor->price = \request("price");
            $professor->save();
        }

        // Если роль студента
        if($request->role == RolesEnum::student){
            $student = Student::where("id", $request->user_id)->first();
            $student->id = $user->id;
            $student->group_id = \request("group_id");
            $student->receipt_date = date('Y-m-d', strtotime(\request("receipt_date")));
            $student->save();
        }

        // измененение
        return response()->json([
            'message' => 'Successfully profile edit!',
            'data' => [
                "id" => $user->id,
                "name" => $passport->firstname,
                "surname" => $passport->secondname,
                "role" => $user->role
            ]
        ]);
    }
}
