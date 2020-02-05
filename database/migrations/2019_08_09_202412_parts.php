<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Carbon\Carbon;

class Parts extends Migration {

	public function up() {

		Schema::create( 'part_categories', function ( Blueprint $table ) {
			$table->bigIncrements( 'id' );
			$table->string( 'name' );
			$table->timestamps();
		} );

		DB::table( 'part_categories' )->insert( [ 'name' => 'Category 1', 'created_at' => now(), 'updated_at' => now(), ] );
		DB::table( 'part_categories' )->insert( [ 'name' => 'Category 2', 'created_at' => now(), 'updated_at' => now(), ] );

		Schema::create( 'parts', function ( Blueprint $table ) {
			$table->bigIncrements( 'id' );
			$table->string( 'name' );
			$table->smallInteger( 'part_category_id' )->unsigned();
			$table->smallInteger( 'user_id' )->unsigned();
			$table->timestamps();
		} );

		Schema::create( 'part_sets', function ( Blueprint $table ) {
			$table->bigIncrements( 'id' );
			$table->bigInteger( 'part_id' )->unsigned()->index();
			$table->smallInteger( 'sort' )->unsigned();
			$table->string( 'file_gcode' )->nullable();
			$table->string( 'file_3mf' )->nullable();
			$table->string( 'file_stl' )->nullable();
			$table->string( 'printer' );
			$table->string( 'filament_material' );
			$table->string( 'filament_diamenter' );
			$table->string( 'nozzle_size' );
			$table->timestamps();
		} );

	}

	public function down() {
	}

}
