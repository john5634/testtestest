<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Part;
use App\PartSet;
use App\PartImage;
use App\PartCategory;

class PartActionsController extends Controller {

	public function __construct() {
	}

	public function addPartSet( Request $request ) {

		$validatedData = $request->validate( [
			'part_id' => 'required',
			'set_printer' => 'required',
			'set_filament_material' => 'required',
			'set_nozzle_size' => 'required',
			'set_filament_diamenter' => 'required',
		] );

		$user = Auth::user();

		$part = Part::find( $request->part_id );
		if ( !$part ) {
			abort( 400, 'Unexisting Part.' );
		}

		$last_set = $part->sets()->orderBy( 'sort', 'desc' )->first();
		$order = 0;
		if ( $last_set ) {
			$order = $last_set->sort + 1;
		}

		$part_set = new PartSet;
		$part_set->sort = $order;
		$part_set->file_gcode = $request->file_set_0_gcode;
		$part_set->file_3mf = $request->file_set_0_3mf ?? '';
		$part_set->file_stl = $request->file_set_0_stl ?? '';
		$part_set->printer = $request->set_printer;
		$part_set->filament_material = $request->set_filament_material;
		$part_set->filament_diamenter = $request->set_filament_diamenter;
		$part_set->nozzle_size = $request->set_nozzle_size;
		$part_set->user()->associate( $user );
		$part_set->part()->associate( $part );
		$part_set->save();

		return redirect( "/part/view/{$part->id}" );

	}

	public function createPart( Request $request ) {

		/*
		 * This is to perfom some basic validation.
		 * https://laravel.com/docs/5.8/validation
		 * A final version of this form should include
		 * better validation rules.
		 */
		$validatedData = $request->validate( [
			'part_category' => 'required',
			'part_name' => 'required',
			'part_images' => 'required',
			'set_visible' => 'required|array',
			'set_printer' => 'required|array',
			'set_filament_material' => 'required|array',
		] );

		
		/*
		* `$user` will hold the currently
		* authenticated user. 
		*/
		$user = Auth::user();

		/**
		 * are we editing
		 */
		$edit_part_id = $request->edit_part_id;
		$edit_part = null;
		if ( $edit_part_id ) {
			$edit_part = Part::find( $edit_part_id );
			if ( !$edit_part ) {
				abort( 402 );
			}
			if ( $edit_part->user_id != $user->id ) {
				abort( 402 );
			}
		}

		/*
		 * We get all POST parameters by using `$request->all`.
		 */
		$data = $request->all();

		/*
		* In the loop down here we just read all part sets.
		* The form holds 10 sets, but only the first one is visible.
		* Every time the user clicks on ADD ANOTHER, a new set is made visible.
		* The `set_visible` parameter will be set to 1 for each
		* part set visible. The ones marked as invisble are just ignored.
		*/
		$sets = [];
		foreach ( $data[ 'set_visible' ] as $set_i => $set_visible ) {
			if ( $set_visible == 1 ) {
				$sets[] = [
					'printer' => $data[ 'set_printer' ][ $set_i ], // Read `set_printer` for set `$i`.
					'filament_material' => $data[ 'set_filament_material' ][ $set_i ], // Read `set_filament_material` for set `$i`.
					'filament_diamenter' => $data[ 'set_filament_diamenter' ][ $set_i ], // Read `set_filament_diamenter` for set `$i`.
					'nozzle_size' => $data[ 'set_nozzle_size' ][ $set_i ], // Read `set_nozzle_size` for set `$i`.
					'file_gcode' => isset( $data[ "file_set_{$set_i}_gcode" ] ) ? $data[ "file_set_{$set_i}_gcode" ] : '', // Read `gcode` for set `$i`.
					'file_3mf' => isset( $data[ "file_set_{$set_i}_3mf" ] ) ? $data[ "file_set_{$set_i}_3mf" ] : '', // Read `3mf` for set `$i`.
					'file_stl' => isset( $data[ "file_set_{$set_i}_stl" ] ) ? $data[ "file_set_{$set_i}_stl" ] : '', // Read `stl` for set `$i`.
				];
			}
		}
		
		/*
		* No we read the part `part_category` parameter and we
		* search in the database (part_categories table) for it.
		*/
		$part_category_id = $data[ 'part_category' ];
		$part_category = PartCategory::find( $part_category_id );
		
		/*
		* Let's now insert a new part in the database.
		* The calls to `associate()` will automatically create the
		* foreign key references to the part category and the user.
		* Docs on how to create model relationships in Laravel:
		* https://laravel.com/docs/5.8/eloquent-relationships.
		*/
		if ( $edit_part ) {
			$part = $edit_part;
			$part->name = $data[ 'part_name' ];
			$part->category()->associate( $part_category );
			$part->save();

			/**
			 * all relationships are actually re-done
			 */
			$part->sets()->delete();
			$part->images()->delete();
			$part->tags()->delete();
		} else {
			$part = new Part;
			$part->name = $data[ 'part_name' ];
			$part->category()->associate( $part_category );
			$part->user()->associate( $user );
			$part->save();
		}

		/**
		 * attach tags
		 */
		$part->attachTags( explode( ',', $data[ 'tags' ] ) );

		/*
		* And now, for each part set, let's  insert a new row in
		* the database. Again, the `associate()` call creates a foreign
		* key to the part we just saved in the database.
		*/
		foreach ( $sets as $i => $set ) {
			$part_set = new PartSet;
			$part_set->sort = $i;
			$part_set->file_gcode = $set[ 'file_gcode' ];
			$part_set->file_3mf = $set[ 'file_3mf' ];
			$part_set->file_stl = $set[ 'file_stl' ];
			$part_set->printer = $set[ 'printer' ];
			$part_set->filament_material = $set[ 'filament_material' ];
			$part_set->filament_diamenter = $set[ 'filament_diamenter' ];
			$part_set->nozzle_size = $set[ 'nozzle_size' ];
			$part_set->user()->associate( $user );
			$part_set->part()->associate( $part );
			$part_set->save();
		}

		/*
		 * images is just a single field (csv like)
		 */
		$part_images = $data[ 'part_images' ];
		$part_images_list = explode( ',', $part_images );
		foreach ( $part_images_list as $p => $one_part_image ) {
			$one_part_image = str_replace( '/storage/', '', $one_part_image );
			$part_image = new PartImage;
			$part_image->order = $p;
			$part_image->file = $one_part_image;
			$part_image->part()->associate( $part );
			$part_image->save();
		}

		return redirect( "/part/view/{$part->id}" );

	}

}
