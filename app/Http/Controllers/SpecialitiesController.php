<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialityRequest;
use App\Models\Speciality;
use Illuminate\Http\JsonResponse;

class SpecialitiesController extends Controller
{
    /**
     *
     * Возврщает все специальности
     *
     * @return JsonResponse
     */
    public function getSpecialities(): JsonResponse
    {
        return response()->json(Speciality::all());
    }

    /**
     *
     * Добавление специальности
     *
     * @param SpecialityRequest $request
     * @return JsonResponse
     */
    public function addSpeciality(SpecialityRequest $request): JsonResponse
    {
        $speciality = new Speciality();
        $speciality->specialty_title = $request->title;
        $speciality->faculty = $request->faculty;
        $speciality->save();

        return response()->json(["message" => "data saved success"]);
    }
}
