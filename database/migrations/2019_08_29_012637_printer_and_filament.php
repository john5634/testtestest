<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrinterAndFilament extends Migration {

	public function up() {

		Schema::create( 'printers', function ( Blueprint $table ) {
			$table->bigIncrements( 'id' );
			$table->string( 'name' );
			$table->timestamps();
		} );

		DB::table( 'printers' )->insert( [ 'name' => 'Printer One', 'created_at' => now(), 'updated_at' => now(), ] );
		DB::table( 'printers' )->insert( [ 'name' => 'Another Printer', 'created_at' => now(), 'updated_at' => now(), ] );

		Schema::create( 'filaments', function ( Blueprint $table ) {
			$table->bigIncrements( 'id' );
			$table->string( 'name' );
			$table->timestamps();
		} );

		DB::table( 'filaments' )->insert( [ 'name' => 'Material One', 'created_at' => now(), 'updated_at' => now(), ] );
		DB::table( 'filaments' )->insert( [ 'name' => 'Another Material', 'created_at' => now(), 'updated_at' => now(), ] );

	}

	public function down() {
	}

}
