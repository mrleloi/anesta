/* global jQuery */

jQuery(document).on('action.ready_trx_addons', function() {

	"use strict";

	jQuery( document ).on( 'action.init_hidden_elements', function( e, $container ) {

		if ( $container === undefined ) {
			$container = jQuery( 'body' );
		}

		$container.find( '.sc_layouts_dark_light:not(.sc_layouts_dark_light_inited)' ).each( function() {
			jQuery( this )
				.addClass( 'sc_layouts_dark_light_inited' )
				.on( 'click', function(e) {
					var $self = jQuery( this ),
						$active = $self.find( '.sc_layouts_dark_light_active' ),
						active_mode = $active.hasClass( 'sc_layouts_dark_light_dark' ) ? 'dark' : 'light',
						$next = $active.siblings( '.sc_layouts_dark_light_item' ),
						next_mode = $next.hasClass( 'sc_layouts_dark_light_dark' ) ? 'dark' : 'light',
						schemes = $self.data( 'schemes' );

					// Change an active mode on the switcher
					$self
						.removeClass( 'sc_layouts_dark_light_active_' + active_mode )
						.addClass( 'sc_layouts_dark_light_active_' + next_mode );
					$active.removeClass( 'sc_layouts_dark_light_active' );
					$next.addClass( 'sc_layouts_dark_light_active' );

					// Change shemes in all areas
					if ( schemes ) {
						var i, data;
						for ( i = 0; i < schemes[active_mode].length; i++ ) {
							data = schemes[active_mode][i];
							jQuery( data.selector ).removeClass( 'scheme_' + data.scheme );
						}
						for ( i = 0; i < schemes[next_mode].length; i++ ) {
							data = schemes[next_mode][i];
							jQuery( data.selector ).addClass( 'scheme_' + data.scheme );
						}
					}

					e.preventDefault();
					return false;
				} );
		} );
	} );
} );