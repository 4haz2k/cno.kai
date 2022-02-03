<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use App\Models\Subject;
use Illuminate\Http\Request;

class UserController extends Controller
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

    public function test(){
//       $test = Subject::where("title", "LIKE", '%'. \request('title') . '%');
       //$test = Subject::where("title", "LIKE", '%'. \request('title') . '%')->with(["professors"])->get();
//       $test = Subject::where("title", "LIKE", "%".\request('title')."%")
//           ->with(["professors"])
//           ->get()
//           ->pluck("professors")
//           ->filter()
//           ->flatten()
//           ->all();





        $firstname = \request("firstname");
        $secondname = \request("secondname");
        $thirdname = \request("thirdname");
        $subject = \request("subject");
//        $test = Passport::where("firstname", "LIKE", "%{$firstname}%")
//            ->where("secondname", "LIKE", "%{$secondname}%")
//            ->where("thirdname", "LIKE", "%{$thirdname}%")->with("user.professor.subjects", function ($query) use($subject){
//                $query->where("title", "LIKE", "%{$subject}%");
//            })->get();
        $test = Subject::where("title", "LIKE", "%{$subject}%")
            ->with("professors.user.passport", function ($query) use($firstname, $secondname, $thirdname){
                $query->where("firstname", "LIKE", "%{$firstname}%")
                    ->where("secondname", "LIKE", "%{$secondname}%")
                    ->where("thirdname", "LIKE", "%{$thirdname}%");
            })->get();

        $test_array = [];

        foreach ($test as $item) {
            foreach ($item->professors as $professor){
                if($professor->user->passport != null)
                    $test_array[] = $professor;
            }
        }
        dd($test_array);
    }
}
