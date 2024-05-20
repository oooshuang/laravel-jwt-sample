<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected function output($data, $msg='',$status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $msg,
            'data' => $data
        ], $status);
    }

}
