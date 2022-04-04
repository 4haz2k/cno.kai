<?php

namespace App\Http\Controllers;

use App\Models\Group;
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
}
