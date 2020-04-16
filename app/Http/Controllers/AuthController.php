<?php

namespace App\Http\Controllers;

use App\Address;
use App\Response\ErrorCode;
use App\Response\MyResponse;
use App\Response\ResponseStatus;
use App\Response\ResponseStatusCode;
use Illuminate\Http\Request;
use  App\User;
use Illuminate\Support\Facades\Auth;
use App\Response;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:user',
            'password' => 'required|confirmed'
        ]);

        try {
            $user = new User;
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->is_admin = 0;
            $user->address_id = 70;
            $user->state_id = 1;
            $user->registered_at = date("Y-m-d H:i:s");

            $user->save();
            return Response\MyResponse::generateJson(
                ResponseStatus::OK,
                $user,
                ErrorCode::OK,
                ResponseStatusCode::OK
            );
        } catch (\Exception $e) {
            //todo: return error message

            return MyResponse::generateJson(
                ResponseStatus::FAIL,
                null,
                ErrorCode::FAIL,
                ResponseStatusCode::FAIL
            );
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);
        if (! $token = Auth::attempt($credentials)) {

            return Response\MyResponse::generateJson(
                ResponseStatus::FAIL,
                null,
                ErrorCode::UNAUTHORIZED,
                ResponseStatusCode::FAIL
            );
        }
        $result = User::where('email',$request["email"])->join('address','user.address_id','=', 'address.id')->first(['first_name',
            'last_name', 'email', 'latitude',
            'longitude','phone_number',
            'name', 'postal_code','image', 'user.id']);
        $result->token = 'bearer '.$token;
        return MyResponse::generateJson(
            ResponseStatus::OK,
            $result,
            ErrorCode::OK,
            ResponseStatusCode::OK
        );
    }

    public function logout() {
        try {
            Auth::guard('api')->logout();
            return Response\MyResponse::generateJson(
                ResponseStatus::OK,
                null,
                ErrorCode::OK,
                ResponseStatusCode::OK
            );
        } catch (\Exception  $e) {
            return Response\MyResponse::generateJson(
                ResponseStatus::FAIL,
                null,
                ErrorCode::FAIL,
                ResponseStatusCode::FAIL
            );
        }
    }
}
