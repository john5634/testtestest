
@extends( 'layouts.app' )

@section( 'content' )
<div class="container">
	<div class="row">
		<div class="col-3">
		</div>
		<div class="col-9">
			<form>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label for="search_part_name">Part Name</label>
						<input type="text" class="form-control" id="search_part_name">
					</div>
					<div class="form-group col-md-5">
						<label for="search_tags">Tags</label>
						<select class="form-control" id="search_tags" multiple="multiple">
							@foreach ( $tags as $tag )
								<option>{{ $tag->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-md-3">
						<label for="search_sorting">Sort By</label>
						<select class="custom-select" id="search_sorting">
							<option value='newest'>Newest</option>
							<option value='oldest'>Oldest</option>
							<option value='popularity'>Popularity</option>
						</select>
					</div>
				</div>
				<button type="button" class="btn btn-primary mb-3" id="search_button">Search</button>
			</form>
		</div>
		<div class="col-3">

			<!-- <strong>Liked</strong> -->
			<div class="form-check">
				<input class="filter-check form-check-input" type="checkbox" value="liked|1" id="liked_check_1">
				<label class="form-check-label" for="liked_check_1">
					Liked
				</label>
			</div>
			<button type="button" class="do-filters btn btn-primary btn-sm mt-2 mb-2">Apply</button>

			<hr/>
			<!-- <strong>Category</strong> -->
			@foreach ( $categories as $c => $category )
				<div class="form-check">
					<input class="filter-check form-check-input" type="checkbox" value="category|{{ $category->id }}" id="category_check_{{ $c }}">
					<label class="form-check-label" for="category_check_{{ $c }}">
						{{ $category->name }}
					</label>
				</div>
			@endforeach
			<button type="button" class="do-filters btn btn-primary btn-sm mt-2 mb-2">Apply</button>

			<hr/>
			<!-- <strong>Printer</strong> -->
			@foreach ( $printers as $p => $printer )
				<div class="form-check">
					<input class="filter-check form-check-input" type="checkbox" value="printer|{{ $printer->id }}" id="printer_check_{{ $p }}">
					<label class="form-check-label" for="printer_check_{{ $p }}">
						{{ $printer->name }}
					</label>
				</div>
			@endforeach
			<button type="button" class="do-filters btn btn-primary btn-sm mt-2 mb-2">Apply</button>

			<hr/>
			<!-- <strong>Filament</strong> -->
			@foreach ( $filaments as $f => $filament )
				<div class="form-check">
					<input class="filter-check form-check-input" type="checkbox" value="filament|{{ $filament->id }}" id="filament_check_{{ $f }}">
					<label class="form-check-label" for="filament_check_{{ $f }}">
						{{ $filament->name }}
					</label>
				</div>
			@endforeach
			<button type="button" class="do-filters btn btn-primary btn-sm mt-2 mb-2">Apply</button>

			<hr/>
			<strong>Filament Diameter</strong>
			@foreach ( [ '1.75mm', '2.85mm' ] as $d => $diameter )
				<div class="form-check">
					<input class="filter-check form-check-input" type="checkbox" value="diameter|{{ $diameter }}" id="diameter_check_{{ $d }}">
					<label class="form-check-label" for="diameter_check_{{ $d }}">
						{{ $diameter }}
					</label>
				</div>
			@endforeach
			<button type="button" class="do-filters btn btn-primary btn-sm mt-2 mb-2">Apply</button>

			<hr/>
			<strong>Nozzle Size</strong>
			@foreach ( [ '0.25mm', '0.3mm', '0.4mm', '0.5mm', '0.6mm', '0.8mm', '1.0mm' ] as $n => $nozzle )
				<div class="form-check">
					<input class="filter-check form-check-input" type="checkbox" value="nozzle|{{ $nozzle }}" id="nozzle_check_{{ $n }}">
					<label class="form-check-label" for="nozzle_check_{{ $n }}">
						{{ $nozzle }}
					</label>
				</div>
			@endforeach
			<button type="button" class="do-filters btn btn-primary btn-sm mt-2 mb-2">Apply</button>

		</div>
		<div class="col-9">
			<div class="alert alert-warning" role="alert" style="display: none;" id="no-results">
				No results were found using your search criteria.
			</div>
			<div class="row" id="all-parts-container"></div>
		</div>
	</div>
</div>

<script src="{{ asset( 'js/parts.js' ) }}" defer></script>

@endsection
