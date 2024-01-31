<?php

namespace App\Http\Controllers\Backend;

use App\Hub;
use App\Vendor;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class VendorDistanceCalculationController extends Controller
{

    public function index(Request $request)
    {
        $hubs = Hub::whereNull('deleted_at')->get();
        $vendors = Vendor::whereNull('deleted_at')->get();

        $latLng = [];
        foreach($vendors as $vendor){
            foreach($hubs as $hub){
                $joeyDistance = $this->twopoints_on_earth($hub->hub_latitude, $hub->hub_longitude, $vendor->latitude, $vendor->longitude);
                $latLng[] = [
                  'hub_id' => $hub->id,
                  'vendor_id' => $vendor->id,
                  'distance' => $joeyDistance
                ];
            }

        }

        dd($latLng);
    }

    public function twopoints_on_earth($latitudeFrom, $longitudeFrom,
                                       $latitudeTo,  $longitudeTo)
    {
        $long1 = deg2rad($longitudeFrom);
        $long2 = deg2rad($longitudeTo);
        $lat1 = deg2rad($latitudeFrom);
        $lat2 = deg2rad($latitudeTo);

        //Haversine Formula
        $dlong = $long2 - $long1;
        $dlati = $lat2 - $lat1;

        $val = pow(sin($dlati/2),2)+cos($lat1)*cos($lat2)*pow(sin($dlong/2),2);

        $res = 2 * asin(sqrt($val));

        $radius = 3958.756;

        return ($res*$radius)*1609.344+10;
    }

}
