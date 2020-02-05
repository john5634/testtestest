<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Overtrue\LaravelLike\Traits\CanBeLiked;

class Part extends Model {

	use \Spatie\Tags\HasTags, HasComments, CanBeLiked;

	protected $appends = [
		'created_at_human',
	];

	/**
	 * relationships
	 */
	public function sets() { 
		return $this->hasMany( 'App\PartSet' );
	}

	public function images() { 
		return $this->hasMany( 'App\PartImage' );
	}

	public function userImages() { 
		return $this->hasMany( 'App\CommentImage' );
	}

	public function category() {
		return $this->belongsTo( 'App\PartCategory', 'part_category_id' );
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

}
