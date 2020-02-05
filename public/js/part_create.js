$( () => {

	$( "#form-errors" ).hide();
	$( ".autocomplete-printer" ).autocomplete( { source: printers } );
	$( ".autocomplete-filament" ).autocomplete( { source: filaments } );

	/**
	 * fielpond stuff
	 */
	FilePond.setOptions( {
		server: {
			process: {
				url: '/part/file/process',
				onerror: ( response ) => {
					response = jQuery.parseJSON( response );
					if ( response && response.error ) {
						modal.info( "Error uploading GCODE file", response.error );
					} else {
						modal.info( "Error", "An unknown error occurred when uploading the GCODE file." );
					}
				},
			},
			revert: '/part/file/revert',
			restore: null,
			load: null,
			fetch: null,
		}
	} );

	/**
	 * images
	 */
	partImages.init();

	/**
	 * tags
	 */
	tags.init();

	/**
	 * sets
	 */
	partSets.init();	
	$( "#btn_add_set" ).click( function() {
		partSets.addNewSet();
	} );
	$( ".btn_remove_set" ).click( function() {
		partSets.removeSet( $( this ) );
	} );

	$( "#part-create-form" ).on( 'submit', function( e ) {

		let errs = part.validate().concat( partImages.validate().concat( partSets.validate().concat( tags.validate() ) ) );
		if ( errs.length > 0 ) {
			e.preventDefault();
			modal.info( "Errors", errs.join( '<br/>' ) );
		}

		let ts = tags.getAll();
		$( "#post-tags" ).val( ts.join( ',' ) );

	} );

	// var zip = new JSZip();
	// $( '.my-pond' ).on( 'FilePond:addfilestart', function( e ) {
	// 	let f = e.detail.file.file;
	// 	zip.file( "gcode.gcode", f );
	// 	zip.generateAsync( { type: "base64" } ).then( function( content ) {
	// 	} );
	// } );

} );

let tags = {
	init: () => {
		$( "#tags" ).select2( {
			tags: true,
			tokenSeparators: [ ',', ' ' ]
		} );
	},
	getAll: () => {
		let ts = $( '#tags' ).select2( 'data' );
		let ts_raw = [];
		ts.forEach( ( element, idx ) => {
			ts_raw.push( element.text );
		} );
		return ts_raw;
	},
	validate: () => {
		let errs = [];
		let ts = tags.getAll();
		if ( ts.length == 0 ) {
			errs.push( "Please add at least one tag." );
		}
		return errs;
	}
}

let partImages = {

	l: [], // holds the list of images

	init: () => {
		let pond_image_input = document.querySelector( 'input.file_image' );
		let pond_image = FilePond.create( pond_image_input, { 'labelIdle': 'Drag & Drop an image file' } );
	
		$( '.file_image' ).on( 'FilePond:processfile', function( e ) {
			if ( !e.detail.error ) {
				let image_path = "/storage/" + e.detail.file.serverId;

				partImages.addNew( image_path );
				partImages.renderList();
				partImages.refreshList();

				pond_image.removeFile();
			}
		} );
	},

	refreshList: () => {
		$( "#part_images_container div.one-image a.move-up" ).show();
		$( "#part_images_container div.one-image a.move-down" ).show();
		$( "#part_images_container div.one-image a.move-up" ).first().hide();
		$( "#part_images_container div.one-image a.move-down" ).last().hide();
	},

	moveUp: ( image_path ) => {
		let new_l = [];
		let new_pos = partImages.l.indexOf( image_path ) - 1;
		partImages.l.forEach( ( element, idx ) => {
			if ( idx == ( new_pos ) ) new_l.push( image_path );
			if ( element != image_path ) new_l.push( element );
		} );
		partImages.l = new_l;
	},

	moveDown: ( image_path ) => {
		let new_l = [];
		let new_pos = partImages.l.indexOf( image_path ) + 1;
		partImages.l.forEach( ( element, idx ) => {
			if ( element != image_path ) new_l.push( element );
			if ( idx == ( new_pos ) ) new_l.push( image_path );
		} );
		partImages.l = new_l;
	},

	remove: ( image_path ) => {
		let new_l = [];
		partImages.l.forEach( element => { if ( element != image_path ) new_l.push( element ); } );
		partImages.l = new_l;
	},

	addNew: ( image_path ) => {
		partImages.l.push( image_path );
	},

	renderList: () => {
		$( "#part_images_container" ).empty();
		partImages.l.forEach( element => {
			partImages.renderOne( element );
		} );
		$( "#part_images" ).val( partImages.l.join( ',' ) );
	},
	renderOne: ( image_path ) => {
		$( "#part_images_container" ).append(
			$( "<div>" ).addClass( "one-image" ).html(
				$( "<div>" ).addClass( "thumb" ).html(
					$( "<img/>" ).attr( "src", image_path )
				)
			).append(
				$( "<div>" ).html(
					$( "<a/>" ).addClass( "my-1 move-up" ).attr( "href", "#" ).html( "Move Left" ).click( ( e ) => {
						e.preventDefault();
						partImages.moveUp( image_path );
						partImages.renderList();
						partImages.refreshList();
					} )
				)
			).append(
				$( "<div>" ).html(
					$( "<a/>" ).addClass( "my-1 move-down" ).attr( "href", "#" ).html( "Move Right" ).click( ( e ) => {
						e.preventDefault();
						partImages.moveDown( image_path );
						partImages.renderList();
						partImages.refreshList();
					} )
				)
			).append(
				$( "<div>" ).html(
					$( "<a/>" ).addClass( "my-1 remove" ).attr( "href", "#" ).html( "Remove" ).click( ( e ) => {
						e.preventDefault();
						partImages.remove( image_path );
						partImages.renderList();
						partImages.refreshList();
					} )
				)
			)
		);
	},

	validate: () => {
		let errs = [];
		if ( partImages.l.length == 0 ) {
			errs.push( "You have to upload at least one image." );
		}
		return errs;
	}

}

let part = {
	validate: () => {
		let errs = [];
		let part_name = $.trim( $( "#part_name" ).val() );
		if ( part_name == '' ) {
			errs.push( "<strong>Part Name</strong> is a mandatory field." );
		}
		return errs;
	}
}

let partSets = {

	init: () => {
		$( "#set_0" ).show();
		$( "#set_0" ).find( ".visible-flag" ).val( 1 );
		$( '.file_gcode' ).filepond( { 'labelIdle': 'Drag & Drop your GCODE file' } );
		$( '.file_3mf' ).filepond( { 'labelIdle': 'Drag & Drop your 3MF file' } );
		$( '.file_stl' ).filepond( { 'labelIdle': 'Drag & Drop your STL file' } );
	},

	addNewSet: () => {
		$( ".set_form:hidden" ).first().find( '.visible-flag' ).val( 1 );
		$( ".set_form:hidden" ).first().show();
	},

	removeSet: ( e ) => {
		e.closest( ".set_form" ).find( ".visible-flag" ).val( 0 );
		e.closest( ".set_form" ).hide();
	},

	validate: () => {
		let errs = [];
		let num_sets = 0;
		$( ".set_form:visible" ).each( function( index ) {
			let printer = $.trim( $( this ).find( ".field-printer" ).val() );
			let filament = $.trim( $( this ).find( ".field-filament" ).val() );
			if ( printer == '' ) {
				errs.push( "<strong>Printer Name</strong> is a mandatory field." );
			} else {
				if ( printers.indexOf( printer ) < 0 ) {
					errs.push( "An invalid value was entered for <strong>Printer Name</strong>." );
				}
			}
			if ( filament == '' ) {
				errs.push( "<strong>Filament Material</strong> is a mandatory field." );
			} else {
				if ( filaments.indexOf( filament ) < 0 ) {
					errs.push( "An invalid value was entered for <strong>Filament Material</strong>." );
				}
			}
			let gcode = $( "input[name=file_set_" + num_sets + "_gcode]" ).val();
			if ( gcode == '' ) {
				errs.push( "Please upload a <strong>GCODE</strong> file on each set." );
			}
			num_sets++;
		} );
		if ( num_sets == 0 ) {
			errs.push( "You need to add at least one set." );
		}
		return errs.filter( onlyUnique );
	}

};

/**
 * some helpers
 */

function onlyUnique( value, index, self ) { 
	return self.indexOf( value ) === index;
}