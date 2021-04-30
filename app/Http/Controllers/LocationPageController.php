<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;

class LocationPageController extends BaseController
{
    public function index($location_link){
    	$location = Location::where('link', $location_link)->first();
    	if($location){
	    	return view('location', [
	    		'location' => $location
	    	]);
    	}else{
    		return null;
    	}
    }
}
