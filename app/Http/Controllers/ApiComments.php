<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use App\User;
use App\Part;
use App\CommentImage;

class ApiComments extends Controller {

	private $per_page = 6;

	public function __construct() {
	}

	public function postOne( Request $request, $part_id ) {

		$request->validate( [
			'text' => 'required|max:255',
		] );	

		$part = Part::find( $part_id );
		if ( !$part ) {
			abort( 404 );
		}
		$comment = $part->comment( e( $request->text ) );

		if ( $request->image && !empty( $request->image ) ) {
			$i = new CommentImage;
			$i->comment_id = $comment->id;
			$i->part_id = $part_id;
			$i->file = $request->image;
			$i->save();
		}

		return response()->json( [] );
	}

	public function getAll( Request $request, $part_id ) {
		$part = Part::find( $part_id );
		if ( !$part ) {
			abort( 404 );
		}

		$comments= $part->comments()->orderBy( 'created_at', 'desc' )->get();
		foreach ( $comments as $comment ) {
			$comment->user = User::find( $comment->user_id );
			$comment->created_at_human = $comment->created_at->diffForHumans();
			$comment->comment_new_lines = nl2br( $comment->comment );
			$comment->image = CommentImage::where( 'comment_id', $comment->id )->first();
		}
		return response()->json( $comments );
	}

}
