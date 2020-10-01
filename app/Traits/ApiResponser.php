<?php

namespace App\Traits;

trait ApiResponser
{

    //  return an illuminate/Http/jsonReponse

    //success response
    public static function __success($data, $code = 200)
    {
        return response()->json(['data' => $data], $code);
    }

     //  return an illuminate/Http/jsonReponse

    //error response
    public static function __error($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
}

