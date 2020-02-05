<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartSet extends Model {

	protected $appends = [
		'created_at_human',
		'gcode_basename',
	];

	/**
	 * relationships
	 */
	public function part() { 
		return $this->belongsTo( 'App\Part' );
	}

	public function user() {
		return $this->belongsTo( 'App\User' );
	}

	/**
	 * accesors
	 */
	public function getCreatedAtHumanAttribute() {
		return $this->created_at->diffForHumans();
	}

	public function getGcodeBasenameAttribute() {
		return basename( $this->file_gcode );
	}

}