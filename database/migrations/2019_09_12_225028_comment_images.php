<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommentImages extends Migration {

	public function up() {
		Schema::create( 'comment_images', function (Blueprint $table) {
			$table->increments( 'id' );
			$table->string( 'file' );
			$table->bigInteger( 'comment_id' )->unsigned()->index();
			$table->bigInteger( 'part_id' )->unsigned()->index()->nullable();
			$table->timestamps();
		} );
	}

	public function down() {
	}

}
