<?php


namespace App\Http\Controllers;

use App\Response\ErrorCode;
use App\Response\MyResponse;
use App\Response\ResponseStatus;
use App\Response\ResponseStatusCode;
use App\User;
use App\Address;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UserController extends  Controller {

    private $headers = ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'];
    private $options = JSON_UNESCAPED_UNICODE;

    public function __construct() {
        $this->middleware('auth');
    }

    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => null
        ];
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function getUsers() {
        return response()->json(['users' =>  User::all()], 200);
    }

    public function getUser($id)
    {
        try {
            $user = User::findOrFail($id);
            return MyResponse::generateJson(ResponseStatus::OK,
                $user,
                ErrorCode::OK,
                ResponseStatusCode::OK
                );
        } catch (\Exception $e) {
            return MyResponse::generateJson(ResponseStatus::FAIL,
                null,
                ErrorCode::FAIL,
                ResponseStatusCode::FAIL
            );
        }

    }
    public function getAvatar($id) {
        $path = storage_path('app/public/avatars/'.$id.'.jpg');
        return Response::download($path);
    }

    public function updateUser(Request $request) {
        $this->validate($request, [
            'first_name' => 'string',
            'last_name' => 'string',
            'address_id' => 'int',
            'phone_number' => 'string',
            'email' => 'string',
            'id' => 'required'
        ]);
        $result = "";
        $requestData = array_filter($request->all());

        if ($request->hasFile('image')) {
            $image = $request->file("image");
            Storage::disk('avatars')->put($request['id'].'.jpg', File::get($image));
            $requestData['image'] = 'avatar/'.$request['id'];
        }
        User::where('id',$request->id)->update($requestData);
    }


    public function getUserByEmail(Request $request) {
        try {
            $result = User::where('email',$request["email"])->join('address','user.address_id','=', 'address.id')->first(['first_name',
                'last_name', 'email', 'latitude',
                'longitude','phone_number',
                'name', 'postal_code', 'image', 'user.id']);
            return MyResponse::generateJson(ResponseStatus::OK,
                $result,
                ErrorCode::OK,
                ResponseStatusCode::OK
            );
        } catch (\Exception $e) {
            return MyResponse::generateJson(ResponseStatus::FAIL,
                null,
                ErrorCode::FAIL,
                ResponseStatusCode::FAIL
            );
        }
    }

    public function getUserAddress($id)
    {
        $query  = DB::select("select * from address where id in (select address_id from user where id = $id)");
        if ($query == null) {
            return MyResponse::generateJson(ResponseStatus::NO_DATA_FOUND,
                $query,
                ErrorCode::NO_DATA_FOUND,
                ResponseStatusCode::OK);
        } else {
            return MyResponse::generateJson(ResponseStatus::OK,
                $query,
                ErrorCode::OK,
                ResponseStatusCode::OK);
        }
    }
}
