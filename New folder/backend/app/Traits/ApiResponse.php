<?php

namespace App\Traits;

trait ApiResponse
{
    public function success($msg = '', $data = null)
    {
        return response()->json([
            'msg' => $msg,
            'data' => $data
        ], 200);
    }

    public function error($msg = 'Something went wrong with the request', $data = null)
    {
        return response()->json([
            'msg' => $msg,
            'data' => $data
        ], 500);
    }

    public function data($data = null)
    {
        return response()->json($data);
    }
}
