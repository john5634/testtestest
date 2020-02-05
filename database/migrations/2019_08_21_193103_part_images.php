<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartImages extends Migration {

	public function up() {

		Schema::create( 'part_images', function ( Blueprint $table ) {
			$table->bigIncrements( 'id' );
			$table->bigInteger( 'part_id' )->unsigned()->index();
			$table->string( 'file' );
			$table->smallInteger( 'order' )->default( 1 );
			$table->timestamps();
		} );

	}

	public function down() {
	}

}
