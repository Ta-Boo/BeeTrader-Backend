<?php

namespace App\Http\Controllers;

use App\Listing;
use App\Response\ErrorCode;
use App\Response\MyResponse;
use App\Response\ResponseStatus;
use App\Response\ResponseStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ListingController extends Controller
{

    public function listingInRadius(Request $request) {
        $this->validate($request, [
            'radius' => 'required|integer',
            'lat' => 'required',
            'lon' => 'required'
        ]);

        $lat    = $request['lat'];
        $lon    = $request['lon'];
        $radius = $request['radius'];
        $angle_radius = $radius / (111 * cos($lat));
        $min_lat = $lat - $angle_radius;
        $max_lat = $lat + $angle_radius;
        $min_lon = $lon - $angle_radius;
        $max_lon = $lon + $angle_radius;

        [$min_lon,$max_lon] = $this->swapIfNeeded($min_lon,$max_lon);
        [$min_lat,$max_lat] = $this->swapIfNeeded($min_lat,$max_lat);

        $toBeSelected = ['title','price','seen','longitude','latitude','listing.id','listing.image','type_id'];

        $query = DB::table('listing')
            ->join('user','user.id','=','listing.user_id')
            ->join('address','user.address_id','=','address.id');

        if ($request->has('categories')) {
            foreach ($request->categories as $key => $value) {
                $query->orWhere('type_id','=',$value);
            }
        }
        $query = $query->whereBetween('latitude',[$min_lat,$max_lat])
            ->whereBetween('longitude',[$min_lon,$max_lon])
            ->paginate(20,$toBeSelected,'page',1);

        $result = [];
        foreach ($query as $listing) {
            $distance = $this->distanceBetween([$lat, $lon],[$listing->latitude,$listing->longitude]);
            if ($distance <= $radius) {
                $listing->distance = round($distance,2);
                 unset($listing->latitude);
                 unset($listing->longitude);
                $result[]=$listing;
            }
        }

        return MyResponse::generateJson(ResponseStatus::OK,
            $result,
            ErrorCode::OK,
            ResponseStatusCode::OK);
    }

    function categories(Request $request) {

        return MyResponse::generateJson(ResponseStatus::OK,
            DB::table('listing_type')->get(['type','id']),
            ErrorCode::OK,
            ResponseStatusCode::OK);
    }

    public function listingById(Request $request) {
        $toBeSelected = ['listing.id', 'user_id', 'title', 'description', 'listing.image', 'price', 'seen', 'is_in', 'first_name', 'email','phone_number'];
        $result = Listing::where('listing.id','=',$request['id'])
            ->join('user','listing.user_id','=','user.id')
            ->join('address','address_id', '=', "address.id")
            ->first($toBeSelected);
        $result['id'] = $request['id'];
        return MyResponse::generateJson(ResponseStatus::OK,
            $result,
            ErrorCode::OK,
            ResponseStatusCode::OK);
    }
    public function listingPreview($id) {
        $path = storage_path('app/public/listings/'.$id.'.jpg');
        return Response::download($path);
    }


    public function distanceBetween($from,$to) {
        $r = 6371e3;
        $alpha1 = $this->toRads($from[0]);
        $alpha2 = $this->toRads($to[0]);
        $deltaAlpha = $this->toRads(($to[0]-$from[0]));
        $deltaGamma = $this->toRads(($to[1]-$from[1]));

        $a = pow(sin($deltaAlpha/2), 2) + cos($alpha1) * cos($alpha2)  * pow(sin($deltaGamma/2), 2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $r * $c /1000;
    }
    public function toRads($double) {
        return $double * pi() /180;
    }

    private function swapIfNeeded($a, $b) {
        $placeHolder = 0;
        if($a > $b) {
            $placeHolder = $a;
            $a = $b;
            $b = $placeHolder;
        }
        return [$a,$b];
    }
}
