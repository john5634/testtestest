<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Part;

class ApiPopularity extends Controller {

	public function __construct() {
	}

	public function clicked( Request $request, $part_id ) {
		$part = Part::find( $part_id );
		if ( !$part ) {
			abort( 404 );
		}
		$part->popularity++;
		$part->save();
		return response()->json( [] );
	}

}
