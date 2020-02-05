<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use App\Part;
use App\Printer;
use App\Filament;

class ApiParts extends Controller {

	private $per_page = 6;

	public function __construct() {
	}

	public function getPartsPage( Request $request ) {

		$data = $request->all();
		$search = ( isset( $data[ 'search' ] ) ) ? $data[ 'search' ] : null;
		$filters = ( isset( $data[ 'filters' ] ) ) ? $data[ 'filters' ] : null;

		$parts = Part::with( [ 'user', 'images', ] );

		/**
		 * search
		 */
		if ( $search ) {
			if ( isset( $search[ 'name' ] ) && !empty( $search[ 'name' ] ) ) {
				$trimmed_search_name = trim( $search[ 'name' ] );
				$parts = $parts->where( "name", "like", "%{$trimmed_search_name}%" );
			}
			if ( isset( $search[ 'tags' ] ) && !empty( $search[ 'tags' ] ) ) {
				$parts = $parts->withAnyTags( $search[ 'tags' ] );
			}
		}

		/**
		 * filters
		 */
		if ( $filters ) {
			if ( isset( $filters[ 'liked' ] ) && !empty( $filters[ 'liked' ] ) ) {
				$parts = $this->_filterPartsLiked( $parts );
			}
			if ( isset( $filters[ 'category' ] ) && !empty( $filters[ 'category' ] ) ) {
				$parts = $parts->whereIn( 'part_category_id', $filters[ 'category' ] );
			}
			if ( isset( $filters[ 'printer' ] ) && !empty( $filters[ 'printer' ] ) ) {
				$parts = $this->_filterPartsPrinters( $parts, $filters[ 'printer' ] );
			}
			if ( isset( $filters[ 'filament' ] ) && !empty( $filters[ 'filament' ] ) ) {
				$parts = $this->_filterPartsFilament( $parts, $filters[ 'filament' ] );
			}
			if ( isset( $filters[ 'diameter' ] ) && !empty( $filters[ 'diameter' ] ) ) {
				$parts = $this->_filterPartsDiameter( $parts, $filters[ 'diameter' ] );
			}
			if ( isset( $filters[ 'nozzle' ] ) && !empty( $filters[ 'nozzle' ] ) ) {
				$parts = $this->_filterPartsNozzle( $parts, $filters[ 'nozzle' ] );
			}
		}

		\Log::info( $search[ 'sort' ] );
		if ( $search && isset( $search[ 'sort' ] ) && !empty( $search[ 'sort' ] ) ) {
			if ( $search[ 'sort' ] == 'newest' ) {
				$parts = $parts->orderBy( 'created_at', 'desc' );
			} else if ( $search[ 'sort' ] == 'oldest' ) {
				$parts = $parts->orderBy( 'created_at', 'asc' );
			} else if ( $search[ 'sort' ] == 'popularity' ) {
				$parts = $parts->orderBy( 'popularity', 'desc' );
			}
		} else {
			$parts = $parts->orderBy( 'created_at', 'desc' );
		}

		return response()->json( $parts->simplePaginate( $this->per_page ) );

	}

	public function getPartSets( Request $request ) {

		$data = $request->all();
		$filters = ( isset( $data[ 'filters' ] ) ) ? $data[ 'filters' ] : null;

		$part = Part::find( $request->part_id );
		if ( !$part ) {
			return response()->json( [] );
		}

		$sets = $part->sets();

		/**
		 * filters
		 */
		if ( $filters ) {
			if ( isset( $filters[ 'printer' ] ) && !empty( $filters[ 'printer' ] ) ) {
				$sets = $this->_filterSetsPrinters( $sets, $filters[ 'printer' ] );
			}
			if ( isset( $filters[ 'filament' ] ) && !empty( $filters[ 'filament' ] ) ) {
				$sets = $this->_filterSetsFilament( $sets, $filters[ 'filament' ] );
			}
			if ( isset( $filters[ 'diameter' ] ) && !empty( $filters[ 'diameter' ] ) ) {
				$sets->whereIn( 'filament_diamenter', $filters[ 'diameter' ] );
			}
			if ( isset( $filters[ 'nozzle' ] ) && !empty( $filters[ 'nozzle' ] ) ) {
				$sets->whereIn( 'nozzle_size', $filters[ 'nozzle' ] );
			}
		}

		return response()->json( $sets->with( 'user' )->get() );

	}

	private function _filterSetsPrinters( $sets, $printer_ids ) {
		$printers = Printer::whereIn( 'id', $printer_ids )->get();
		$printer_names = [];
		foreach ( $printers as $printer ) {
			$printer_names[] = $printer->name;
		}
		if ( empty( $printer_names ) ) {
			return $sets;
		}
		return $sets->whereIn( 'printer', $printer_names );
	}

	private function _filterSetsFilament( $sets, $filament_ids ) {
		$filaments = Filament::whereIn( 'id', $filament_ids )->get();
		$filament_names = [];
		foreach ( $filaments as $filament ) {
			$filament_names[] = $filament->name;
		}
		if ( empty( $filament_names ) ) {
			return $sets;
		}
		return $sets->whereIn( 'filament_material', $filament_names );
	}

	private function _filterPartsLiked( $parts ) {
		return $parts->whereHas( 'likes' );
	}

	private function _filterPartsPrinters( $parts, $printer_ids ) {
		$printers = Printer::whereIn( 'id', $printer_ids )->get();
		$printer_names = [];
		foreach ( $printers as $printer ) {
			$printer_names[] = $printer->name;
		}
		if ( empty( $printer_names ) ) {
			return $parts;
		}
		return
			$parts->whereHas( 'sets', function ( Builder $query ) use ( $printer_names ) {
				$query->whereIn( 'printer', $printer_names );
			} );
	}

	private function _filterPartsFilament( $parts, $filament_ids ) {
		$filaments = Filament::whereIn( 'id', $filament_ids )->get();
		$filament_names = [];
		foreach ( $filaments as $filament ) {
			$filament_names[] = $filament->name;
		}
		if ( empty( $filament_names ) ) {
			return $parts;
		}
		return
			$parts->whereHas( 'sets', function ( Builder $query ) use ( $filament_names ) {
				$query->whereIn( 'filament_material', $filament_names );
			} );
	}

	private function _filterPartsDiameter( $parts, $diameters ) {
		return
			$parts->whereHas( 'sets', function ( Builder $query ) use ( $diameters ) {
				$query->whereIn( 'filament_diamenter', $diameters );
			} );
	}

	private function _filterPartsNozzle( $parts, $nozzles ) {
		return
			$parts->whereHas( 'sets', function ( Builder $query ) use ( $nozzles ) {
				$query->whereIn( 'nozzle_size', $nozzles );
			} );
	}

}
