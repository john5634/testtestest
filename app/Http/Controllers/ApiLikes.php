<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\User;
use App\Part;

class ApiLikes extends Controller {

	public function __construct() {
	}

	public function like( Request $request, $part_id ) {
		$part = Part::find( $part_id );
		if ( !$part ) {
			abort( 404 );
		}
		$user = Auth::user();
		$user->toggleLike( $part );
		return response()->json( [] );
	}

	public function liked( Request $request, $part_id ) {
		$part = Part::find( $part_id );
		if ( !$part ) {
			abort( 404 );
		}
		$user = Auth::user();
		return response()->json( [ 'liked' => $user->hasLiked( $part ), ] );
	}

	public function likes( Request $request ) {
		$user = Auth::user();
		return response()->json( [ 'likes' => $user->likes, ] );
	}

}
