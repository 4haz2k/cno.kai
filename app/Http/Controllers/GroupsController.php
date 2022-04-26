<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\Speciality;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    /**
     *
     * Возврщает все группы
     *
     * @return JsonResponse
     */
    public function getGroups(): JsonResponse
    {
        return response()->json(Group::all());
    }

    /**
     *
     * Добавление группы
     *
     * @param GroupRequest $request
     * @return JsonResponse
     */
    public function addGroup(GroupRequest $request): JsonResponse
    {
        $specialty = Speciality::where("faculty", \request("speciality"))->first();

        $subject = new Group();

        $subject->group_code = \request("group");
        $subject->specialty_id = $specialty->id;

        $subject->save();

        return response()->json(["message" => "data saved success"]);
    }
}
