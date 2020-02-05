
$( () => {

	infiniteScroll.init();
	search.init();
	filters.init();
	likes.likes();

} );

let search = {
	s: {},
	init: () => {
		$( "#search_tags" ).select2( {
			tokenSeparators: [ ',', ' ' ]
		} );
		$( "#search_button" ).click( function() {
			search.do();
		} );
	},
	do: () => {
		let ts = $( '#search_tags' ).select2( 'data' );
		search.s = { tags: [], name: '' };
		ts.forEach( ( element, idx ) => {
			search.s.tags.push( element.text );
		} );
		search.s.name = $( "#search_part_name" ).val();
		search.s.sort = $( "#search_sorting" ).val();
		infiniteScroll.reset();
		parts.reset();
		parts.getPage();
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
		infiniteScroll.reset();
		parts.reset();
		parts.getPage();
	}
};

let infiniteScroll = {
	in_zone: false,
	margin: 100,
	init: () => {
		$( window ).scroll( function () {
			if ( $( document ).height() - infiniteScroll.margin - $( this ).height() > $( this ).scrollTop() ) {
				infiniteScroll.in_zone = false;
			}
			if ( !infiniteScroll.in_zone ) {
				if ( $( document ).height() - infiniteScroll.margin - $( this ).height() < $( this ).scrollTop() ) {
					infiniteScroll.in_zone = true;
					parts.getPage();
				}
			}
		} ); 
	},
	reset: () => {
		infiniteScroll.in_zone = false;
	}
}

let parts = {
	next_page: "/a/parts",
	getPage: () => {
		if ( parts.next_page ) {
			$.post( parts.next_page, { 'search': search.s, 'filters': filters.f }, function( data ) {
				if ( data && data.next_page_url ) {
					parts.next_page = data.next_page_url;
				} else {
					parts.next_page = null;
				}
				parts.showPage( data.data );
			} );
		}
	},
	reset: () => {
		$( "#all-parts-container" ).empty();
		parts.next_page = "/a/parts";
	},
	showPage: ( data ) => {
		if ( data.length == 0 ) {
			$( "#no-results" ).show(); 
		} else {
			$( "#no-results" ).hide(); 
			data.forEach( element => {
				$( "#all-parts-container" ).append(
					parts.buildOne( element )
				);
			} );
		}
	},
	buildOne: ( o ) => {
		let liked = $( "<span>" );
		if ( likes.likedPart( o.id ) ) {
			liked.html( $( "<i>" ).addClass( "fas fa-heart float-right" ) );
		}
		return $( "<div>" ).addClass( "col-lg-4 col-sm-6 col-xs-12 mb-4" ).html(
			$( "<div>" ).addClass( "parts-part" ).html(
				$( "<div>" ).addClass( "parts-header" ).html(
					$( "<h6>" ).html( o.name ).append( liked )
				).append(
					$( "<p>" ).addClass( "pb-0 mb-0" ).html(
						$( "<small>" ).html( "by " + o.user.name + " " + o.created_at_human )
					)
				)
			).append(
				$( "<div>" )
					.addClass( "parts-thumb-container" )
					.css( 'background-image', 'url( "' + "/storage/" + o.images[ 0 ].file + '" )' )
			)
		).click( () => {
			location.href = '/part/view/' + o.id;
		} );
	}
}

let likes = {
	l: [],
	init: () => {
	},
	likes: () => {
		$.get( '/a/user/likes', function( data ) {
			data.likes.forEach( ( element, index ) => {
				likes.l.push( element.likable_id );
			} );
			parts.getPage();
		} );
	},
	likedPart: ( part_id ) => {
		if ( likes.l.indexOf( part_id ) >= 0 ) {
			return true;
		}
		return false;
	}
}
