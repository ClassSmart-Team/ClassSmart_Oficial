<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse(
        $data = null,
        string $message = "Operacion Exitosa",
        int $status = 200
    )
    {
        return response()->json([
            "status" => true,
            "message" => $message,
            "data" => $data,
            "error" => null
        ], $status);
    }

    protected function errorResponse( string $message = "Error",
                                      int $status = 400,
                                             $error = null)
    {
        return response()->json([
            "status" => false,
            "message" => $message,
            "data" => null,
            "error" => $error
        ], $status);
    }
}

