<?php

namespace App\Utils;

use Illuminate\Validation\ValidationException;

class Utils
{
    public static function handleException($exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
              'status'  => 'fail',
              'errors'  => $exception->errors()
            ], 422);
        }
        return response()->json([
              'status' => 'fail',
              'message' => $exception->getMessage()
            ], 500);
    }
}
