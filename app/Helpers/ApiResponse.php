<?php

namespace App\Helpers;

class ApiResponse
{
  public static function success($data)
  {
    return response()->json([
      'success' => true,
      'data' => $data,
    ]);
  }

  public static function notFound($message = 'Not found')
  {
    return response()->json([
      'success' => false,
      'message' => $message,
    ], 404);
  }

  public static function error($e, $code = 500)
  {
    return response()->json([
      'success' => false,
      'message' => config('app.debug') ? $e->getMessage() : 'Something went wrong',
    ], $code);
  }

  public static function unauthorized($message = 'Unauthorized')
  {
    return response()->json([
      'success' => false,
      'message' => $message,
    ], 401);
  }
}
