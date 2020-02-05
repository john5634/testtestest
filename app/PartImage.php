<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartImage extends Model {

	public function part() { 
		return $this->belongsTo( 'App\Part' );
	}

}