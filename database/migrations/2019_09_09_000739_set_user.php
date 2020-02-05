<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetUser extends Migration {

	public function up() {
		DB::table( 'parts' )->delete();
		DB::table( 'part_sets' )->delete();
		DB::table( 'part_images' )->delete();
		Schema::table( 'part_sets', function ( Blueprint $table ) {
			$table->bigInteger( 'user_id' )->after( 'part_id' );
		} );
	}

	public function down() {
	}

}
