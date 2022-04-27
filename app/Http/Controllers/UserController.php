<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessorRequest;
use App\Models\Passport;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     *
     * Обновление данных преподавателя
     *
     * @param ProfessorRequest $request
     * @return JsonResponse
     */
    public function updateProfessorData(ProfessorRequest $request): JsonResponse
    {
        $user = User::find($request->user_id);

        if(!$user)
            return response()->json(["error" => "user by id {$request->user_id} not found"]);

        // passport
        $user->passport->secondname = \request("surname");
        $user->passport->firstname = \request("name");
        $user->passport->thirdname = \request("patronymic");
        $user->passport->birthday = \request("date_of_birth");
        $user->passport->series = \request("serial");
        $user->passport->number = \request("number");
        $user->passport->date_of_issue = \request("date_of_issue");
        $user->passport->issued = \request("issued_by");
        $user->passport->division_code = \request("department_code");

        // user
        $user->phone = \request("telephone");
        $user->login = \request("email");

        //professor
        $user->professor->department = \request("faculty");
        $user->professor->date_of_commencement_of_teaching_activity = \request("exp");
        $user->professor->position = \request("position");
        $user->professor->ITN = \request("INN");
        $user->professor->INILA = \request("SNILS");
        $user->professor->personnel_number = \request("personal_number");
        $user->professor->description = \request("description");

        // placeOfResidence
        $user->passport->placeOfResidence->country = \request("country");
        $user->passport->placeOfResidence->region = \request("state");
        $user->passport->placeOfResidence->locality = \request("city");
        $user->passport->placeOfResidence->district = \request("district");
        $user->passport->placeOfResidence->street = \request("street");
        $user->passport->placeOfResidence->house = \request("house");
        $user->passport->placeOfResidence->frame = \request("entrance");
        $user->passport->placeOfResidence->apartment = \request("apt");

        $user->push();

        return response()->json(["message" => "data saved success"]);
    }

    public function updateStudentData(){

    }
}
