<?php

namespace App\Http\Controllers;

use App\Listing;
use App\Response\ErrorCode;
use App\Response\MyResponse;
use App\Response\ResponseStatus;
use App\Response\ResponseStatusCode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ListingController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function listingInRadius(Request $request) {
        $this->validate($request, [
            'radius' => 'required|integer',
            'lat' => 'required',
            'lon' => 'required',
            'page' => 'required'
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

        $toBeSelected = ['title','price','seen','longitude','latitude','listing.id','listing.image','type_id','email'];

        $query = DB::table('listing')
            ->join('user','user.id','=','listing.user_id')
            ->join('address','user.address_id','=','address.id');

        if ($request->has('categories')) {
            foreach ($request->categories as $key => $value) {
                $query->orWhere('type_id','=',$value);
            }
        }
        if ($request->text != "") {
            $query = $query->where('description', 'like','%'.$request->text.'%')
            ->orWhere('title','like','%'.$request->text.'%');
        }
        $query = $query->whereBetween('latitude',[$min_lat,$max_lat])
            ->whereBetween('longitude',[$min_lon,$max_lon])
            ->orderBy('id','desc')
            ->paginate(8,$toBeSelected,'page',$request->page
            );

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

    function categories() {

        return MyResponse::generateJson(ResponseStatus::OK,
            DB::table('listing_type')->get(['type','id']),
            ErrorCode::OK,
            ResponseStatusCode::OK);
    }

    public function listingById(Request $request) {
        $toBeSelected = ['listing.id', 'user_id', 'title', 'description', 'listing.image', 'price', 'seen',
            'is_in', 'first_name', 'email','phone_number','listing.type_id'];
        $result = Listing::where('listing.id','=',$request['id'])
            ->join('user','listing.user_id','=','user.id')
            ->join('address','address_id', '=', "address.id")
            ->first($toBeSelected);
        $result['id'] = $request['id'];
        $listing = Listing::find($request->id);
        $listing->seen += 1;
        $listing->save();
        return MyResponse::generateJson(ResponseStatus::OK,
            $result,
            ErrorCode::OK,
            ResponseStatusCode::OK);
    }

    public function listingPreview($id) {
        $path = storage_path('app/public/listings/'.$id.'.jpg');
        return Response::download($path);
    }

    function uploadListing(Request $request) {
//        $this->validate($request, [
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int',
            'state_id' => 'required|string',
            'type_id' => 'required|int',
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|int',
        ]);

        if ($validator->fails()) {
            return MyResponse::generateJson(
                ResponseStatus::VALIDATION_FAIL,
                null,
                ErrorCode::FAIL,
                ResponseStatusCode::FAIL);
        }
        if($request->hasFile('image')) {
            $listing = new Listing();
            $listing->user_id = $request->user_id;
            $listing->state_id = $request->state_id;
            $listing->type_id = $request->type_id;
            $listing->title = $request->title;
            $listing->description = $request->description;
            $listing->price = $request->price;
            $listing->save();
            $image = $request->file("image");
            Storage::disk('listings')->put($listing->id .'.jpg', File::get($image));
            $listing->image = 'listingPreview/'. $listing->id;
            $listing->save();
            return MyResponse::generateJson(
                ResponseStatus::OK,
                null,
                ErrorCode::OK,
                ResponseStatusCode::OK);
        } else {
            return MyResponse::generateJson(
                ResponseStatus::FAIL,
                null,
                ErrorCode::FAIL,
                ResponseStatusCode::FAIL);
        }
    }

    public function updateListing(Request $request) {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|string',
            'id' => 'required|int',
            'type_id' => 'required|int',
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|int',
        ]);

        if ($validator->fails()) {
            return MyResponse::generateJson(
                ResponseStatus::VALIDATION_FAIL,
                $validator->errors(),
                ErrorCode::FAIL,
                ResponseStatusCode::FAIL);
        }
        $listing = Listing::find($request->id);

        if ($request->hasFile('image')) {
            $listing->image = "listingPreview/".$request->id;
            $image = $request->file("image");
            Storage::disk('listings')->put($request['id'].'.jpg', File::get($image));
            $requestData['image'] = 'avatar/'.$request['id'];
        }
        $listing->state_id = $request->state_id;
        $listing->type_id = $request->type_id;
        $listing->title = $request->title;
        $listing->description = $request->description;
        $listing->price = $request->price;
        $listing->save();
        return MyResponse::generateJson(
            ResponseStatus::OK,
            null,
            ErrorCode::OK,
            ResponseStatusCode::OK);
    }


    public function deleteListing(Request $request) {
        $listing = Listing::find($request->id);
        if ($listing == null) {
            return MyResponse::generateJson(
                ResponseStatus::NO_DATA_FOUND,
                null,
                ErrorCode::NO_DATA_FOUND,
                ResponseStatusCode::FAIL);
        }
         Listing::where('id',$request->id)->delete();
        return MyResponse::generateJson(
            ResponseStatus::OK,
            null,
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
