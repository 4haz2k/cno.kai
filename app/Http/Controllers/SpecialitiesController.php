<?php

namespace App\Http\Controllers;

use App\Models\Speciality;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
