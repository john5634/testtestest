<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller {

	private $gcode_greps = [ "M500", "M129" ];

	public function __construct() {
		$this->gcode_greps = explode( ',', config( 'teaboware.uploads.gcode_malicious_codes' ) );
	}

	public function upload( Request $request ) {

		$all = $request->all();

		$file = null;
		$extension = [];
		$index = null;
		foreach ( $all as $k => $v ) {
			$file_parts = explode( '_', $k );
			$extension = $file_parts[ 3 ];
			$index = $file_parts[ 2 ];
			$level = $file_parts[ 1 ];
		}

		$file = $request->file( "file_{$level}_{$index}_{$extension}" );

		if ( !$file ) {
			return response()->json( [ 'error' => 'File not found on the payload' ], 422 );
		}

		$path = $file->path();
		$original_name = $file->getClientOriginalName();
		$real_extension = $file->extension();

		if ( $extension == "gcode" ) {
			$malicious_code = $this->_gcodeGrep( $path );
			if ( !empty( $malicious_code ) ) {
				$msg = env(
					"GCODE_MALICIOUS_CODE_{$malicious_code}",
					"\"{$malicious_code}\" is a possible malicious code, please upload gcode that does not contain \"{$malicious_code}\""
				);
				return response()->json( [ 'error' => $msg, ], 422 );
			}
		}

		if ( $extension == "image" ) {
			$extension = $real_extension;
		}
		return $file->storeAs( "public/{$extension}", uniqid() . '.' . $extension );

	}

	private function _gcodeGrep( $path ) {
		$file = fopen( $path, "r" );
		while ( !feof( $file ) ) {
			$line = fgets( $file );
			foreach ( $this->gcode_greps as $grep ) {
				$pos = stripos( $line, $grep );
				if ( $pos !== false ) {
					fclose( $file );
					return $grep;
				}
			}
		}
		fclose( $file );
		return null;
	}

	public function remove( Request $request ) {

		try {
			Storage::disk( 'public' )->delete( $request->getContent() );
		} catch ( \Exception $e ) {
		}

		return Response::make( '', 200 );

	}

}
