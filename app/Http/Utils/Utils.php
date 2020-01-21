<?php

namespace App\Utils;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        if ($exception instanceof ModelNotFoundException) {
            $modelName = class_basename($exception->getModel());
            $modelName = Str::kebab($modelName);
            $modelName = str_replace('-', ' ', $modelName);
            $modelName = ucfirst($modelName);
            return response()->json([
              'status'  => 'fail',
              'message' => $modelName.' not found'
            ], 404);
        }
        return response()->json([
              'status' => 'fail',
              'message' => $exception->getMessage()
            ], 500);
    }

    public static function returnData($data)
    {
        return response()->json([
          'status' => 'success',
          'data'   => $data
        ]);
    }

    public static function returnSuccess($message)
    {
        return response()->json([
          'status' => 'success',
          'message'=> $message
        ]);
    }
}
