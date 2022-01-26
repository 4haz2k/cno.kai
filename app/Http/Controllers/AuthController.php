<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Passport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = ["login" => \request('email'), "password" => \request("password")];

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * User registration
     * @param Request $request
     * @return JsonResponse
     */
    public function registration(Request $request): JsonResponse
    {
        $request = $request->all();
        // Валидация адреса
        $address_request = [
            "country" => $request['actual_living_place']['country'],
            "state" => $request['actual_living_place']['state'],
            "city" => $request['actual_living_place']['city'],
            "district" => $request['actual_living_place']['district'],
            "street" => $request['actual_living_place']['street'],
            "house" => $request['actual_living_place']['house'],
            "entrance" => $request['actual_living_place']['entrance'],
            "apt" => $request['actual_living_place']['apt'],
        ];

        $address_validator = Validator::make($address_request, Address::rules());

        // Валидация паспорта
        $passport_validation = [
            "serial" => $request['passport']['serial'], // series
            "number" => $request['passport']['number'],
            "date_of_issue" => $request['passport']['date_of_issue'],
            "issued_by" => $request['passport']['issued_by'], // issued
            "department_code" => $request['passport']['department_code'], // division_code
            "scan" => $request['passport']['scan'],
            //"place_of_residence_id" => $address->id,
            "surname" => $request['passport']['surname'], // secondname
            "name" => $request['passport']['name'], // firstname
            "patronymic" => $request['passport']['patronymic'], // thirdname
            "date_of_birth" => $request['passport']['date_of_birth'], // birthday
            "sex" => $request['passport']['sex'],
        ];

        $passport_validator = Validator::make($passport_validation, Passport::rules());

        // валидация пользователя
        $user_validation = [
            "actual_place_of_residence_id" => request('actual_place_of_residence_id'),
            "email" => $request['email'],
            'password' => $request['password'],
            "telephone" => $request['telephone'],
            "role" => "student",
        ];

        $user_validator = Validator::make($user_validation, User::rules());

        if($address_validator->fails() or $passport_validator->fails() or $user_validator->fails()){ // если проверка не пройдена
            $errors = ["errors" => []];

            if($address_validator->fails())
                array_push($errors["errors"], [
                    "error_address" => [
                        "message" => "Address validation error",
                        "errors" => $address_validator->messages()->get("*")
                    ]
                ]);

            if($passport_validator->fails())
                array_push($errors["errors"], [
                    "error_passport" => [
                        "message" => "Passport validation error",
                        "errors" => $passport_validator->messages()->get("*")
                    ]
                ]);

            if($user_validator->fails())
                array_push($errors["errors"], [
                    "error_user" => [
                        "message" => "User validation error",
                        "errors" => $address_validator->messages()->get("*")
                    ]
                ]);


            return response()->json($errors); // возвращаем ошибки
        }

        // Регистрируем новый адрес
        $address = new Address();
        $address->country = $address_request["country"];
        $address->region = $address_request["state"];
        $address->locality = $address_request["city"];
        $address->district = $address_request["district"];
        $address->street = $address_request["street"];
        $address->house = $address_request["house"];
        $address->frame = $address_request["entrance"];
        $address->apartment = $address_request["apt"];
        $address->save();

        // регистрируем новый паспорт
        $passport = new Passport();
        $passport->series = $passport_validation["serial"];
        $passport->number = $passport_validation["number"];
        $passport->date_of_issue = $passport_validation["date_of_issue"];
        $passport->issued = $passport_validation["issued_by"];
        $passport->division_code = $passport_validation["department_code"];
        $passport->scan = $passport_validation["scan"];
        $passport->place_of_residence_id = $address->id;
        $passport->secondname = $passport_validation["surname"];
        $passport->firstname = $passport_validation["name"];
        $passport->thirdname = $passport_validation["patronymic"];
        $passport->birthday = $passport_validation["date_of_birth"];
        $passport->sex = $passport_validation["sex"];
        $passport->save();

        // регистрируем нового пользователя
        $user = new User();
        $user->id = $passport->id;
        $user->login = $user_validation["email"];
        $user->password = Hash::make($user_validation["password"]);
        $user->phone = $user_validation["telephone"];
        $user->role = $user_validation["role"];
        $user->actual_place_of_residence_id = $address->id;
        $user->save();

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
