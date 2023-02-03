<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImageController extends Controller
{

    public function uploadimage(ImageRequest $request){

        $photo = fopen($request->file("file"),'rb');
        $response = Http::attach('file',$photo)->post("http://127.0.0.1:5000/success");
        fclose($photo);
        return $response;
    }
}
