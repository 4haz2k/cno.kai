<?php

namespace App\Http\Controllers;

use App\Enum\TimeEnum;
use App\Models\Order;
use App\Services\DocumentService;
use App\Services\SecurityService;
use App\Services\SubmissionForPaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class FilesController extends Controller
{
    /**
     *
     * Создание договора
     *
     * @return JsonResponse|BinaryFileResponse
     */
    public function getTreaty(){
        $security = new SecurityService();
        $document = new DocumentService();

        if($security->checkHash(\request("order_id"), \request("sign"))){
            $file = $document->getDocument(\request("order_id"));
            if($file["status"]){
                return response()->download(public_path()."/documents/".$file["link"], "Договор на оказание услуг.docx");
            }
            else{
                return response()->json($file["info"]);
            }
        }
        else{
            return response()->json(["error" => "Bad sign"]);
        }
    }

    /**
     *
     * Создание архива договоров
     *
     * @param DocumentService $documentService
     * @param ZipArchive $zipArchive
     * @return JsonResponse|BinaryFileResponse
     */
    public function getTreatyAll(DocumentService $documentService, ZipArchive $zipArchive)
    {
        $orders = Order::with("student.user.passport")->where("create_date", "<=", Carbon::now("Europe/Moscow"));

        switch (\request("time")){
            case TimeEnum::day:
                $orders = $orders->where("create_date", ">=",
                        Carbon::now("Europe/Moscow")->subDay()->format("Y-m-d H:i:s"));
                break;
            case TimeEnum::week:
                $orders = $orders->where("create_date", ">=",
                    Carbon::now("Europe/Moscow")->subWeek()->format("Y-m-d H:i:s"));
                break;
            case TimeEnum::month:
                $orders = $orders->where("create_date", ">=",
                    Carbon::now("Europe/Moscow")->subMonth()->format("Y-m-d H:i:s"));
                break;
            default:
                return response()->json(["message" => "The given data was invalid."], 422);
        }

        $orders = $orders->get();

        if($orders->isEmpty()){
            return response()->json([],204);
        }

        if ($zipArchive->open(public_path("documents/treats.zip"), ZipArchive::CREATE) === TRUE)
        {
            foreach ($orders as $order){
                $result = $documentService->getDocument($order->id);
                $student_passport = $order->student->user->passport;
                $student_name = $student_passport->secondname." ".$student_passport->firstname." ".$student_passport->thirdname." ";

                if($result["status"]){
                    $zipArchive->addFile(
                        public_path("documents/{$result["link"]}"),
                        $student_name . Carbon::createFromTimeString($order->create_date)->format("d.m.Y H:i:s"). ".docx"
                    );
                }
            }
            $zipArchive->close();
        }

        return response()->download(public_path("documents/treats.zip"))->deleteFileAfterSend(true);
    }

    /**
     * Получение представления на выплаты за месяц
     * @param SubmissionForPaymentService $submissionForPaymentService
     * @return JsonResponse|BinaryFileResponse
     */
    public function getSubmission(SubmissionForPaymentService $submissionForPaymentService){
        $file = $submissionForPaymentService->getSubmission();

        if($file != null){
            return response()->download($file)->deleteFileAfterSend(true);
        }
        else{
            return response()->json([], 204);
        }
    }
}
