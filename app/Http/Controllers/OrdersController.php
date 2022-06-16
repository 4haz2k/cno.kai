<?php

namespace App\Http\Controllers;

use App\Enum\StatusEnum;
use App\Http\Requests\ChangeStatusOfOrderRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\TimeTable;
use App\Services\DocumentService;
use App\Services\SecurityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class OrdersController extends Controller
{
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
            $data = $data->where("status", "!=", StatusEnum::lastStatus);
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

        return response()->json($data + ["statuses" => config('statics.statuses'), "total" => $total_pages]);
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

        $orders = Order::with([
            "student" => function ($q) { $q->select(["id"]);},
            "student.user" => function ($q) { $q->select(["id", "role"]);},
            "student.user.passport" => function ($q) { $q->select(["id", "firstname", "secondname", "thirdname"]);},
            "timeTable" => function ($q) { $q->select(["id", "subject_of_professor_id"]);},
            "timeTable.subjectOfProfessor" => function($q){ $q->select(["id", "subject_id", "professor_id"]); },
            "timeTable.subjectOfProfessor.subject" => function($q){ $q->select(["id", "title"]); },
        ])
            ->select(["id", "status", "hash", "student_id", "timetable_id"])
            ->whereHas("timeTable.subjectOfProfessor.professor", function ($q) { $q->where("id", \request("professor_id")); })
            ->orderBy("create_date", "DESC");


        if(request("status")){
            $orders = $orders->where("status", request("status"));
        }

        if(request("subject_id")){
            $orders = $orders->whereHas("timeTable.subjectOfProfessor.subject", function ($q) { $q->where("id", \request("subject_id")); });
        }

        $orders = $orders->paginate(\request("page_size") ? : 10)->toArray();

        unset(
            $orders["links"],
            $orders["to"],
            $orders["prev_page_url"],
            $orders["path"],
            $orders["next_page_url"],
            $orders["last_page_url"],
            $orders["first_page_url"],
            $orders["from"],
            $orders["last_page"]
        );

        return response()->json($orders + ["statuses" => config('statics.statuses')]);
    }

    /**
     *
     * Создание заявки
     *
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function createOrder(OrderRequest $request): JsonResponse
    {
        $timetable = TimeTable::with("subjectOfProfessor.professor")->where("id", $request->timetable_id)->first();
        $price = $timetable->subjectOfProfessor->professor->price;

        // Сохранение нового заказа
        $order = new Order();
        $order->student_id = $request->student_id;
        $order->timetable_id = $request->timetable_id;
        $order->service_id = $request->service_id;
        $order->status = "Проверка";
        $order->price = bcdiv($price * (double)$request->number_of_hours, 1, 2);

        $currentTime = Carbon::now("Europe/Moscow");
        $order->create_date = $currentTime->toDateTimeString();

        $order->number_of_lessons = $request->number_of_hours;

        $order->save();

        // сохранение хэша
        $security = new SecurityService();
        $order->hash = $security->encryptData($order->id);
        $order->update();

        // Создание документа
        $document = new DocumentService();
        $result = $document->getDocument($order->id);

        // Ответ
        if($result["status"]){
            return response()->json(["order_id" => $order->id, "message" => "order created"]);
        }
        else{
            return response()->json(["order_id" => $order->id,"message" => "order created, but failed to create file"]);
        }
    }

    /**
     *
     * Изменение статуса заказа с проверкой следующего разрешенного статуса
     *
     * @param ChangeStatusOfOrderRequest $request
     * @return JsonResponse
     */
    public function changeStatus(ChangeStatusOfOrderRequest $request): JsonResponse
    {
        $statuses = config("statics.statuses");
        $order = Order::where("id", $request->order_id)->first();

        // Находим ключ статуса, который указан в заказе
        $statusKeyToInsert = array_search($order->status, $statuses);

        // Находим следущий ключ, который разрешен для изменения
        $allowedNextStatusKey = ($statusKeyToInsert + 1) > (count($statuses) - 1) ? count($statuses) - 1 : ($statusKeyToInsert + 1);

        // Проверяем, соответсвует ли запрашиваемый статус изменения с тем, который разрешён для изменения
        if($statuses[$allowedNextStatusKey] != $request->status){
            return response()->json(["error" => "Next status can't be {$request->status}"], 422);
        }

        // Сохраняем
        $order->status = $request->status;
        $order->save();

        return response()->json(["message" => "data saved success"]);
    }
}
