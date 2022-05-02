<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessorOrAdminRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\Address;
use App\Models\Passport;
use App\Models\Professor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
//    /**
//     * Create a new AuthController instance.
//     *
//     * @return void
//     */
//    public function __construct()
//    {
//        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
//    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
//        $credentials = ["login" => \request('email'), "password" => \request("password")];
//
//        if (! $token = auth()->attempt($credentials)) {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }
//
//        return $this->respondWithToken($token);

        $user = User::where("login", \request("email"));

        if(!$user->first())
            return response()->json(['error' => 'Bad login'], 401);

        if (!Hash::check(\request("password"), $user->first()->password))
            return response()->json(['error' => 'Bad password'], 401);

        $user = $user->with(["passport.placeOfResidence", "student.group.speciality", "professor"])->get();

        $passport = $user->pluck("passport")[0];

        $group = $user->pluck("student.group");

        switch ($user[0]->role){
            case "STUDENT":
                $data = [
                    "id" => $user[0]->id,
                    "passport" => $passport,
                    "faculty" => $group[0]->speciality->faculty,
                    "email" => $user[0]->login,
                    "telephone" => $user[0]->phone,
                    "group" => $group[0]->group_code,
                    "img" => $user[0]->img,
                    "role" => $user[0]->role
                ];
                break;
            case "PREPOD":
                $data = [
                    "id" => $user[0]->id,
                    "passport" => $passport,
                    "email" => $user[0]->login,
                    "telephone" => $user[0]->phone,

                    "price" => $user[0]->professor->price,
                    "exp" => $user[0]->professor->date_of_commencement_of_teaching_activity,
                    "INN" => $user[0]->professor->ITN,
                    "SNILS" => $user[0]->professor->INILA,
                    "personal_number" => $user[0]->professor->personnel_number,

                    "img" => $user[0]->img,
                    "role" => $user[0]->role,
                    "description" => $user[0]->professor->description
                ];
                break;

            case "ADMIN":
                $data = [
                    "id" => $user[0]->id,
                    "passport" => $passport,
                    "email" => $user[0]->login,
                    "telephone" => $user[0]->phone,
                    "img" => $user[0]->img,
                    "role" => $user[0]->role
                ];
                break;

            default:
                $data = ["error" => "Undefined role: {$user[0]->role}"];
                break;
        }

        return response()->json($data);
    }

    /**
     * User registration
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
        $passport->scan = "*scan*";
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
        if($request->role == "PREPOD"){
            $professor = new Professor();
            $professor->id = $user->id;
            $professor->position = \request("position");
            $professor->personnel_number = \request("personal_number");
            $professor->ITN = \request("INN");
            $professor->INILA = \request("SNILS");
            $professor->department = \request("faculty");
            $professor->date_of_commencement_of_teaching_activity = date('Y-m-d', strtotime(\request("exp")));
            $professor->price = \request("price");
            $professor->save();
        }

        // Если роль студента
        if($request->role == "STUDENT"){
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
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json([
            "user" => auth()->user()::with(["passport", "passport.placeOfResidence", "actualPlaceOfResidence"])->get(),
//            "token" => [
//                'access_token' => auth()->refresh(),
//                'token_type' => 'bearer',
//                'expires_in' => $this->guard()->factory()->getTTL() * 60
//            ]
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);

    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
