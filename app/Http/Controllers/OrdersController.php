<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
//    }

    /**
     *
     * Возвращает список заказов пользователя отсортированных по дате(сначала новые) соответсвующих заданным фильтрам
     * parms: uid - id пользователя, page - номер страницы, page_size - количество заявок на одной странице, new - вернуть только незавершенные заявки
     * subject_id - id предмета, status - статус, professor_id - id препода
     *
     * @return JsonResponse
     */
    public function getOrders(): JsonResponse
    {
        // выбираем только нужные данные и добавляем связи
        $data = Order::with([
            "timeTable" => function($q){ $q->select(["id", "subject_of_professor_id"]); },
            "timeTable.subjectOfProfessor" => function($q){ $q->select(["id", "subject_id", "professor_id"]); },
            "timeTable.subjectOfProfessor.subject" => function($q){ $q->select(["id", "title"]); },
            "timeTable.subjectOfProfessor.professor" => function($q){ $q->select(["id"]); },
            "timeTable.subjectOfProfessor.professor.user" => function($q){ $q->select(["id"]); },
            "timeTable.subjectOfProfessor.professor.user.passport" => function($q){ $q->select(["id", "secondname", "firstname", "thirdname"]); },
        ]);

        // есть ли в запросе "id пользователя"
        if(\request("uid")){
            $data = $data->where("student_id", \request("uid"));
        }

        // есть ли в запросе "вернуть только незавершенные заявки"
        if((bool)\request("new")){
            $data = $data->where("status", "!=", "Исполнено");
        }

        // есть ли в запросе "id предмета"
        if(\request("subject_id")){
            $data = $data->whereHas("timeTable.subjectOfProfessor.subject", function ($query){ $query->where("id", \request("subject_id")); });
        }

        // есть ли в запросе "статус"
        if(\request("status")){
            $data = $data->where("status", \request("status"));
        }

        // есть ли в запросе "id препода"
        if(\request("professor_id")){
            $data = $data->whereHas("timeTable.subjectOfProfessor.professor", function ($query){ $query->where("id", \request("professor_id")); });
        }

        // пагинируем и сортируем по дате (сначала новые)
        $data = $data->orderBy("create_date", "DESC")->paginate(\request("page_size") ? : 10);

        // скрываем ненужные атрибуты
        $data->makeHidden([
            "student_id",
            "timetable_id",
            "service_id",
            "price",
            "treaty",
            "create_date",
            "number_of_lessons",
        ]);

        // подсчитываем сколько всего заказов
        $total_pages = $data->total();

        // преобразуем в массив, чтобы удалить ненужные поля пагинации
        $data = $data->toArray();

        // удаляю ненужные поля пагинации
        unset(
            $data["links"],
            $data["to"],
            $data["prev_page_url"],
            $data["path"],
            $data["next_page_url"],
            $data["last_page_url"],
            $data["first_page_url"],
            $data["from"],
            $data["last_page"]
        );

        return response()->json($data + ["statuses" => ["Ожидает исполнения", "Исполнено"], "total" => $total_pages]);
    }

    /**
     *
     * Получение одного заказа по id
     * parms: order_id
     *
     * @return JsonResponse
     */
    public function getSingleOrder(): JsonResponse
    {
        $data = Order::where("id", \request("order_id"))
            ->with([
                "service" => function($q){ $q->select(["id", "title"]); },
                "student" => function($q){ $q->select(["id"]); },
                "student.user" => function($q){ $q->select(["id"]); },
                "student.user.passport" => function($q){ $q->select(["id", "secondname", "firstname", "thirdname"]); },
                "timeTable",
                "timeTable.subjectOfProfessor" => function($q){ $q->select(["id", "subject_id", "professor_id"]); },
                "timeTable.subjectOfProfessor.subject" => function($q){ $q->select(["id", "title"]); },
                "timeTable.subjectOfProfessor.professor" => function($q){ $q->select(["id"]); },
                "timeTable.subjectOfProfessor.professor.user" => function($q){ $q->select(["id"]); },
                "timeTable.subjectOfProfessor.professor.user.passport" => function($q){ $q->select(["id", "secondname", "firstname", "thirdname"]); },])
            ->first();

        if(!$data){
            return response()->json(["error" => "Order not found"], 401);
        }

        $data->makeHidden([
            "student_id",
            "timetable_id",
            "service_id",
            "price",
            "treaty",
            "create_date",
            "number_of_lessons",
        ]);

        return response()->json($data);
    }

    /**
     *
     * Получение списка заказов преподавателя
     *
     * @return JsonResponse
     */
    public function getByProfessor(): JsonResponse
    {
        if(!\request("professor_id")){
            return response()->json(["error" => "parameter professor_id empty"]);
        }

        $orders = Order::whereHas("timeTable.subjectOfProfessor.professor", function ($q) { $q->where("id", \request("professor_id")); })
            ->orderBy("create_date", "DESC")
            ->get();

        return response()->json($orders);
    }
}
