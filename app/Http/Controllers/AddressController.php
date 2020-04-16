<?php

namespace App\Http\Controllers;

use App\Address;
use App\Response\ErrorCode;
use App\Response\MyResponse;
use App\Response\ResponseStatus;
use App\Response\ResponseStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function addresses(Request $request)
    {
//        return Address::where('name', 'LIKE','%')->get();
        $filter = $request->filter;
        $result = Address::where(function ($query) use ($filter) {
            $query->where('name', 'LIKE', '%'.$filter.'%' )
                ->orWhere('postal_code', 'LIKE', '%'.$filter.'%');
        });
        return MyResponse::generateJson(
            ResponseStatus::OK,
            $result->get(),
            ErrorCode::OK,
            ResponseStatusCode::OK
        );
    }

}
