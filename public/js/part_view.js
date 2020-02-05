
$( () => {

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
	$( '.file_gcode' ).filepond( { 'labelIdle': 'Drag & Drop your GCODE file' } );
	$( '.file_3mf' ).filepond( { 'labelIdle': 'Drag & Drop your 3MF file' } );
	$( '.file_stl' ).filepond( { 'labelIdle': 'Drag & Drop your STL file' } );	

	/**
	 * autocomplete
	 */

	$( ".autocomplete-printer" ).autocomplete( { source: printers } );
	$( ".autocomplete-filament" ).autocomplete( { source: filaments } );

	/**
	 * the carousel
	 */

	partImages.init();

	/**
	 * add set form
	 */

	$( "#set-add-form" ).on( 'submit', function( e ) {

		let errs = partSets.validate();
		if ( errs.length > 0 ) {
			e.preventDefault();
			modal.info( "Errors", errs.join( '<br/>' ) );
		}

	} );

	/**
	 * filters
	 */

	filters.init();

	/**
	 * initial load
	 */
	partSets.reset();
	partSets.get();

	/**
	 * comments
	 */
	comments.init();

	/**
	 * likes
	 */
	likes.init();
	likes.liked();

} );

let likes = {
	init: () => {
		$( "#like" ).click( () => {
			$.post( '/a/part/' + part_id + '/like', function( data ) {
				likes.liked();
			} );	
		} );
	},
	liked: () => {
		$.get( '/a/part/' + part_id + '/like', function( data ) {
			$( "#like" ).find( "i" ).removeClass( "fas" ).addClass( "far" );
			if ( data && data.liked ) {
				$( "#like" ).find( "i" ).removeClass( "far" ).addClass( "fas" );
			}
		} );	
	}
}

let popularity = {
	clicked: () => {
		$.post( '/a/part/' + part_id + '/popularity', function( data ) {} );	
	}
}

let userImages = {
	i: [],
	carousel: () => {
		$( '.user-images-carousel' ).owlCarousel( {
			loop: true,
			margin: 10,
			nav: true,
			responsive:{
				0: { items: 1 },
				300: { items: 2 },
				600: { items: 3 },
				900: { items: 4 },
			}
		} )
	},
	add: ( i ) => {
		userImages.i.push( i );
	},
	reset: () => {
		userImages.i = [];
		$( "#user-images-carousel" ).empty();
		$( "#user-images-carousel" ).append(
			$( "<div>" ).addClass( "user-images-carousel owl-carousel owl-theme" )
		);
		$( "#user-images-info" ).hide();
	},
	render: () => {
		if ( userImages.i.length == 0 ) {
			$( "#user-images-info" ).show();
		} else {
			let c = $( ".user-images-carousel" );
			userImages.i.forEach( ( element, index ) => {
				c.append( $( "<img>" ).attr( "src", element ) )
			} );
			userImages.carousel();
		}
	}
};

let comments = {
	init: () => {
		$( '#comment-file' ).filepond( { 'labelIdle': 'Drag & Drop an image' } );
		$( "#post-comment" ).click( () => {
			comments.postOne();
		} );
		comments.getAll();
	},
	postOne: () => {
		let image = $( "input[name=file_comment_0_image]" ).val();
		$.post( '/a/part/' + part_id + '/comment', { 'text': $( "#comment-textarea" ).val(), 'image': image, }, function( data ) {
			$( "#comment-textarea" ).val( '' );
			$( '#comment-file' ).filepond( 'removeFiles' );
			comments.getAll();
		} ).fail( function() {
			modal.info( "Error", "Error when trying to post your comment." );
		} );
	},
	getAll: () => {
		$.get( '/a/part/' + part_id + '/comment', function( data ) {
			userImages.reset();
			comments.render( data );
			userImages.render();
		} );
	},
	render: ( comments ) => {
		let r = $( "#all-comments" );
		r.empty();
		comments.forEach( function( element ) {
			let img = $( "<div>" );
			if ( element.image && element.image.file ) {
				img = $( "<div>" )
					.addClass( "comment-image-container" )
					.css( 'background-image', 'url( "' + "/storage/" + element.image.file + '" )' )
				userImages.add( "/storage/" + element.image.file );
			}
			r.append(
				$( "<p>" ).addClass( "mb-0 pb-0 mt-4" ).html( element.comment_new_lines )
			).append(
				img
			).append(
				$( "<p>" ).html(
					$( "<small>" ).html(
						"by " + element.user.name + " " + element.created_at_human
					)
				)
			)
		} );
	}
}

let partImages = {

	init: () => {
		$( '.part-images-carousel' ).owlCarousel( {
			loop: true,
			margin: 10,
			nav: true,
			responsive:{
				0: {
					items:1
				}
			}
		} );
	}
	
};

let filters = {
	f: {},
	init: () => {
		$( ".do-filters" ).click( function() {
			filters.do();
		} );
	},
	do: () => {
		filters.f = {};
		$( ".filter-check" ).each( function( element ) {
			let filter_name = $( this ).val().split( '|' )[ 0 ];
			if ( !filters.f[ filter_name ] ) {
				filters.f[ filter_name ] = [];
			}
			if ( $( this ).prop( "checked" ) ) {
				filters.f[ filter_name ].push( $( this ).val().split( '|' )[ 1 ] );
			}
		} );
		partSets.reset();
		partSets.get();
	}
};

let partSets = {

	url: "/a/sets",

	validate: () => {
		let errs = [];

		let printer = $.trim( $( ".field-printer" ).val() );
		let filament = $.trim( $( ".field-filament" ).val() );
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
		let gcode = $( "input[name=file_set_0_gcode]" ).val();
		if ( gcode == '' ) {
			errs.push( "Please upload a <strong>GCODE</strong> file." );
		}
		return errs;
	},

	get: () => {
		$.post( partSets.url, { 'part_id': part_id, 'filters': filters.f }, function( data ) {
			partSets.show( data );
		} );
	},
	reset: () => {
		$( "#all-sets-container" ).empty();
	},
	show: ( data ) => {
		if ( data.length == 0 ) {
			$( "#no-results" ).show(); 
		} else {
			$( "#no-results" ).hide(); 
			data.forEach( element => {
				$( "#all-sets-container" ).append(
					partSets.buildOne( element )
				);
			} );
		}
	},
	buildOne: ( o ) => {

		let btns = $( "<div>" ).addClass( "col-12" );

		if ( o.file_gcode ) {
			btns.append(
				$( "<a>" )
					.addClass( "btn btn-sm btn-info mr-2" )
					.attr( "href", "/storage/" + o.file_gcode )
					.attr( "download", "gcode-" + part_id + "-" + o.id + ".gcode" )
					.html( "Download GCODE file" )
					.click( () => { popularity.clicked(); } )
			).append(
				$( "<a>" )
					.addClass( "btn btn-sm btn-warning mr-2" )
					.attr( "href", "/gcode-preview/?f=" + o.gcode_basename )
					.attr( "target", "_blank" )
					.html( "Preview" )
			)
		}
		if ( o.file_3mf ) {
			btns.append(
				$( "<a>" )
					.addClass( "btn btn-sm btn-info mr-2" )
					.attr( "href", "/storage/" + o.file_3mf )
					.attr( "download", "gcode-" + part_id + "-" + o.id + ".3mf" )
					.html( "Download 3MF file" )
					.click( () => { popularity.clicked(); } )
			);
		}
		if ( o.file_stl ) {
			btns.append(
				$( "<a>" )
					.addClass( "btn btn-sm btn-info" )
					.attr( "href", "/storage/" + o.file_stl )
					.attr( "download", "gcode-" + part_id + "-" + o.id + ".stl" )
					.html( "Download STL file" )
					.click( () => { popularity.clicked(); } )
			);
		}

		return $( "<div>" ).addClass( "row mt-4" ).html(
			$( "<div>" ).addClass( "col-md-6 col-xs-12" ).html(
				$( "<dl>" ).html(
					$( "<dt>" ).html( "Printer" )
				).append(
					$( "<dd>" ).html( o.printer )
				).append(
					$( "<dt>" ).html( "Filament Material" )
				).append(
					$( "<dd>" ).html( o.filament_material )
				)
			)
		).append(
			$( "<div>" ).addClass( "col-md-6 col-xs-12" ).html(
				$( "<dl>" ).html(
					$( "<dt>" ).html( "Filament Diameter" )
				).append(
					$( "<dd>" ).html( o.filament_diamenter )
				).append(
					$( "<dt>" ).html( "Nozzle Size" )
				).append(
					$( "<dd>" ).html( o.nozzle_size )
				)
			)
		).append(
			btns
		).append(
			$( "<div>" ).addClass( "col-12" ).html(
				$( "<p>" ).html(
					$( "<small>" ).html(
						"by " + o.user.name + " " + o.created_at_human
					)
				)
			).append(
				"<hr/>"
			)
		)
	}
}
