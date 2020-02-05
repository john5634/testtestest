<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;

use App\PartCategory;
use App\Filament;
use App\Printer;
use App\Part;

class PartController extends Controller {

	public function __construct() {
	}

	public function create() {

		$categories = PartCategory::all( [ 'id', 'name' ] );
		$printers = Printer::all( [ 'id', 'name' ] );
		$filaments = Filament::all( [ 'id', 'name' ] );
		$tags = \Spatie\Tags\Tag::get();
		return view( 'part_create', compact( 'categories', 'printers', 'filaments', 'tags' ) );

	}

	public function edit( Request $request, $id ) {

		$categories = PartCategory::all( [ 'id', 'name' ] );
		$printers = Printer::all( [ 'id', 'name' ] );
		$filaments = Filament::all( [ 'id', 'name' ] );
		$tags = \Spatie\Tags\Tag::get();

		$user = Auth::user();
		$part = Part::with( [ 'sets', 'user', 'images', 'tags', 'sets.user' ] )->find( $id );
		if ( $part->user_id != $user->id ) {
			abort( 402 );
		}
		$part = $part->toArray();

		return view( 'part_create', compact( 'part', 'categories', 'printers', 'filaments', 'tags' ) );

	}

	public function view( Request $request, $id ) {

		$part = Part::find( $id );
		if ( !$part ) {
			abort( 404 );
		}
		$printers = Printer::all( [ 'id', 'name' ] );
		$filaments = Filament::all( [ 'id', 'name' ] );

		return view( 'part_view', compact( 'part', 'printers', 'filaments' ) );

	}

	public function viewAll( Request $request ) {

		$tags = \Spatie\Tags\Tag::get();
		$printers = Printer::all( [ 'id', 'name' ] );
		$filaments = Filament::all( [ 'id', 'name' ] );
		$categories = PartCategory::all( [ 'id', 'name' ] );
		return view( 'part_view_all', compact( 'tags', 'printers', 'filaments', 'categories' ) );

	}

}
