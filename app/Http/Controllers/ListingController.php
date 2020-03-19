<?php

namespace App\Http\Controllers;

use App\Response\ErrorCode;
use App\Response\MyResponse;
use App\Response\ResponseStatus;
use App\Response\ResponseStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListingController extends Controller
{

    public function listingInRadius(Request $request)
    {
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
        $toBeSelected = 'title, price, seen, longitude,latitude, listing.id';
        $query =  DB::select("select $toBeSelected from listing join user u on listing.user_id = u.id join address a on u.address_id = a.id
                                        WHERE latitude BETWEEN $min_lat AND $max_lat
                                                AND longitude BETWEEN $min_lon AND $max_lon");
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
}
