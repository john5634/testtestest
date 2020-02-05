
@extends( 'layouts.app' )

@section( 'content' )
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-10">

			<form method="post" action="/part/create/post" enctype="multipart/form-data" id="part-create-form">

				<input type="hidden" id="edit_part_id" name="edit_part_id" value="">

				<div class="form-row mb-4">
					<div class="form-group col-md-6">
						<input type="text" class="form-control" id="part_name" name="part_name" placeholder="Part Name">
					</div>
					<div class="form-group col-md-6">
						<select class="form-control" id="part_category" name="part_category">
							@foreach ( $categories as $category )
								<option value='{{ $category->id }}'>{{ $category->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-md-12">
						<input type="hidden" class="form-control" id="post-tags" name="tags">
						<label>Tags</label>
						<select class="form-control" id="tags" multiple="multiple">
							@foreach ( $tags as $tag )
								<option value="{{ $tag->id }}">{{ $tag->name }}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="col-lg-4 col-md-6 col-sm-8 col-xs-12">
						<input type="file" class="my-pond file_image" name="file_part_1_image" id="file_part_1_image" />
					</div>
				</div>
				<div class="form-row">
					<div class="col-12" id="part_images_container">
					</div>
					<input type="hidden" class="form-control" id="part_images" name="part_images">
				</div>

				@for ( $i = 0; $i <= 10; $i++ )
				<div id="set_{{ $i }}" class="set_form" style="display: none">
					<input type="hidden" class="visible-flag" name="set_visible[]" value="0">
					<hr>
					<div class="form-row">
						<div class="form-group col-md-6">
							<input type="text" class="form-control autocomplete-printer field-printer" name="set_printer[]" placeholder="Printer">
						</div>
						<div class="form-group col-md-6">
							<input type="text" class="form-control autocomplete-filament field-filament" name="set_filament_material[]" placeholder="Filament material">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Filament Diameter</label>
							<select class="form-control field-filament-diameter" name="set_filament_diamenter[]">
								<option value="1.75mm">1.75mm</option>
								<option value="2.85mm">2.85mm</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>Nozzle Size</label>
							<select class="form-control  field-nozzle-size" name="set_nozzle_size[]">
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
							<input type="file" class="my-pond file_gcode" name="file_set_{{ $i }}_gcode" id="file_set_{{ $i }}_gcode" />
						</div>
						<div class="col-md-4 col-xs-12">
							<input type="file" class="my-pond file_3mf" name="file_set_{{ $i }}_3mf" id="file_set_{{ $i }}_3mf" />
						</div>
						<div class="col-md-4 col-xs-12">
							<input type="file" class="my-pond file_stl" name="file_set_{{ $i }}_stl" id="file_set_{{ $i }}_stl" />
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<button type="button" class="btn btn-secondary btn-sm float-right mb-2 btn_remove_set">Remove this set</button>
						</div>
					</div>
				</div>
				@endfor
				<div class="form-row">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary btn-md float-right ml-2" id="post-part-button">Save</button>
						<button type="button" class="btn btn-secondary btn-md float-right" id="btn_add_set">Add another set</button>
					</div>
				</div>

			</form>

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

<?php
if ( isset( $part ) ) {
	echo "let part_edit = " . json_encode( $part ) . ";";
} else {
	echo "let part_edit = NULL";
}
?>

</script>

<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.min.js" defer></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js" defer></script>
<!-- <script src="{{ asset( 'vendor/jszip/jszip.min.js' ) }}" defer></script> -->
<script src="{{ asset( 'js/part_create.js' ) }}" defer></script>


@if ( isset( $part ) )
<script src="{{ asset( 'js/part_edit.js' ) }}" defer></script>
@endif

@endsection
