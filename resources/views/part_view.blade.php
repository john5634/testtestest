
@extends( 'layouts.app' )

@section( 'content' )
<div class="container">
	<div class="row">
		<div class="col-md-5 col-xs-12">
			<div class="part-images-carousel owl-carousel owl-theme">
				@foreach ( $part->images as $image )
					<img src="/storage/{{ $image->file }}"/>
				@endforeach
			</div>
		</div>
		<div class="col-md-7 col-xs-12">
			<dl>
				<dt>Name</dt>
				<dd>
					{{ $part->name }}
				</dd>
				<dt>Category</dt>
				<dd>{{ $part->category->name }}</dd>
				<dt>Tags</dt>
				<dd>
					@foreach ( $part->tags as $tag )
						<span class="badge badge-secondary">
							{{ $tag->name }}
						</span>
					@endforeach
				</dd>
				<dd>
					<button type="button" class="btn btn-sm btn-light" id="like">
						<i class="far fa-heart"></i> Like
					</button>
				</dd>
			</dl>
		</div>
		<div class="col-12 mt-4">

			<nav>
				<div class="nav nav-tabs" id="nav-tab" role="tablist">
					<a class="nav-item nav-link active" id="nav-files-tab" data-toggle="tab" href="#nav-files" role="tab" aria-controls="nav-files" aria-selected="true">Files</a>
					<a class="nav-item nav-link" id="nav-comments-tab" data-toggle="tab" href="#nav-comments" role="tab" aria-controls="nav-comments" aria-selected="false">Comments</a>
					<a class="nav-item nav-link" id="nav-images-tab" data-toggle="tab" href="#nav-images" role="tab" aria-controls="nav-images" aria-selected="false">User Images</a>
				</div>
			</nav>

			<div class="tab-content" id="nav-tabContent">

				<div class="tab-pane fade show active" id="nav-files" role="tabpanel" aria-labelledby="nav-files-tab">

					<div class="row">

						<div class="col-xs-12 col-sm-3 mt-4">

							<strong>Printer</strong>
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

							<strong>Filament</strong>
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

						<div class="col-xs-12 col-sm-9">

							<div class="alert alert-warning mt-4" role="alert" style="display: none;" id="no-results">
								No results were found using your search criteria.
							</div>
							<div id="all-sets-container" class="mb-4"></div>

							<!-- add set form -->

							<h4>Add new set to part</h4>
							<form method="post" action="/part/set/add/post" enctype="multipart/form-data" id="set-add-form">

								<input type="hidden" value="{{ $part->id }}" name="part_id" id="part_id">

								<div class="form-row">
									<div class="form-group col-md-6">
										<input type="text" class="form-control autocomplete-printer field-printer" name="set_printer" placeholder="Printer">
									</div>
									<div class="form-group col-md-6">
										<input type="text" class="form-control autocomplete-filament field-filament" name="set_filament_material" placeholder="Filament material">
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-6">
										<label>Filament Diameter</label>
										<select class="form-control" name="set_filament_diamenter">
											<option value="1.75mm">1.75mm</option>
											<option value="2.85mm">2.85mm</option>
										</select>
									</div>
									<div class="form-group col-md-6">
										<label>Nozzle Size</label>
										<select class="form-control" name="set_nozzle_size">
											<option value="0.25mm">0.25mm</option>
											<option value="0.3mm">0.3mm</option>
											<option value="0.4mm" selected>0.4mm</option>
											<option value="0.5mm">0.5mm</option>
											<option value="0.6mm">0.6mm</option>
											<option value="0.8mm">0.8mm</option>
											<option value="1.0mm">1.0mm</option>
										</select>
									</div>
								</div>

								<div class="row">
									<div class="col-md-4 col-xs-12">
										<input type="file" class="my-pond file_gcode" name="file_set_0_gcode" id="file_set_0_gcode" />
									</div>
									<div class="col-md-4 col-xs-12">
										<input type="file" class="my-pond file_3mf" name="file_set_0_3mf" id="file_set_0_3mf" />
									</div>
									<div class="col-md-4 col-xs-12">
										<input type="file" class="my-pond file_stl" name="file_set_0_stl" id="file_set_0_stl" />
									</div>
								</div>

								<div class="row">
									<div class="col-12">
										<button type="submit" class="btn btn-primary mt-4">Add set</button>
									</div>
								</div>
			
							</form>

						</div>

					</div>

				</div>

				<div class="tab-pane fade" id="nav-comments" role="tabpanel" aria-labelledby="nav-comments-tab">

					<div class="row mt-4">
						<div class="col-md-6 offset-md-3 col-sm-12">
							<form>
								<div class="form-group">
									<textarea class="form-control" id="comment-textarea" rows="3"></textarea>
								</div>
								<div class="form-group">
									<input type="file" class="my-pond" name="file_comment_0_image" id="comment-file" />
								</div>
								<button type="button" class="btn btn-primary" id="post-comment">Post</button>
							</form>
						</div>
						<div class="col-md-6 offset-md-3 col-sm-12" id="all-comments">
						</div>
					</div>

				</div>

				<div class="tab-pane fade" id="nav-images" role="tabpanel" aria-labelledby="nav-images-tab">

					<div class="row mt-4">
						<div class="col-md-10 offset-md-1 col-sm-12" id="user-images-info">
							<div class="alert alert-primary" role="alert">
								No user images were yet added.
							</div>
						</div>
						<div class="col-md-10 offset-md-1 col-sm-12" id="user-images-carousel">
							<div class="user-images-carousel owl-carousel owl-theme"></div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>

<script>

let printers = [
	@foreach ( $printers as $printer )
		'{{ $printer->name }}',
	@endforeach
];

let filaments = [
	@foreach ( $filaments as $filament )
		'{{ $filament->name }}',
	@endforeach
];

let part_id = {{ $part->id }};

</script>

<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.min.js" defer></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js" defer></script>
<script src="{{ asset( 'js/part_view.js' ) }}" defer></script>

@endsection
