
$( () => {

	$( "#edit_part_id" ).val( part_edit.id );
	partEdit.init();

} );

let partEdit = {

	init: () => {
		$( "#part_name" ).val( part_edit.name );
		$( "#part_category" ).val( part_edit.part_category_id );

		let tag_ids = [];
		part_edit.tags.forEach( element => {
			tag_ids.push( element.id );
		} );
		$( "#tags" ).val( tag_ids );
		$( "#tags" ).trigger( "change" );

		part_edit.images.forEach( element => {
			partImages.addNew( "/storage/" + element.file );
		} );
		partImages.renderList();
		partImages.refreshList();

		part_edit.sets.forEach( ( element, index ) => {
			if ( index > 0 ) {
				partSets.addNewSet();
			}

			$( "#set_" + index ).find( ".field-printer" ).val( element.printer );
			$( "#set_" + index ).find( ".field-filament" ).val( element.filament_material );
			$( "#set_" + index ).find( ".field-filament-diameter" ).val( element.filament_diamenter );
			$( "#set_" + index ).find( ".field-nozzle-size" ).val( element.nozzle_size );

			$( "#set_" + index ).find( ".file_gcode" ).filepond( 'addFile', "/storage/" + element.file_gcode );
			if ( element.file_3mf ) {
				$( "#set_" + index ).find( ".file_3mf" ).filepond( 'addFile', "/storage/" + element.file_3mf );
			}
			if ( element.file_stl ) {
				$( "#set_" + index ).find( ".file_stl" ).filepond( 'addFile', "/storage/" + element.file_stl );
			}
		} );

	}

};
