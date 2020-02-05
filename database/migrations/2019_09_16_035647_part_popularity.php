<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartPopularity extends Migration {

	public function up() {
		Schema::table( 'parts', function (Blueprint $table) {
			$table->integer( 'popularity' )->unsigned()->index()->default( 0 )->after( 'user_id' );
		} );
	}

	public function down() {
	}

}
