<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Http\JsonResponse;

class GroupsController extends Controller
{
    /**
     *
     * Возвращает все группы
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
        $group = new Group();

        $group->group_code = $request->group;
        $group->specialty_id = $request->speciality;

        $group->save();

        return response()->json(["message" => "data saved success"]);
    }
}
