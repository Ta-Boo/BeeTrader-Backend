<?php

namespace App\Http\Controllers;

use App\Response\ErrorCode;
use App\Response\MyResponse;
use App\Response\ResponseStatus;
use App\Response\ResponseStatusCode;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function respondWithToken($token)
    {
        return MyResponse::generateJson(
            ResponseStatus::OK,
            ['token' => 'bearer '.$token],
            ErrorCode::OK,
            ResponseStatusCode::OK
        );
    }
}
