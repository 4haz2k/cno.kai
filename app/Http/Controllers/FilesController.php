<?php

namespace App\Http\Controllers;

use App\Services\DocumentService;
use App\Services\SecurityService;
use Illuminate\Http\Request;

class FilesController extends Controller
{
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
}
