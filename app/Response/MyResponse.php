<?php


namespace App\Response;


use PHPUnit\Util\Json;

class MyResponse
{
    public static function
    generateJson($status, $data, $error_code, $status_code){
        sleep(1.6);
        return response()->json([
            'status' => $status,
            'data' => $data,
            'error_code' => $error_code
        ],$status_code,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }
}

abstract class ResponseStatus  {

    const OK = "Success";
    const FAIL = "Failed";
    const NO_DATA_FOUND = "No data found";
}

abstract class ResponseStatusCode  {

    const OK = 200;
    const FAIL = 401;
    const CONFLICT = 409;
}
abstract class ErrorCode  {

    const OK = 1000;
    const FAIL = -1000;
    const NO_DATA_FOUND = -1001;
    const UNAUTHORIZED = -1002;

}
