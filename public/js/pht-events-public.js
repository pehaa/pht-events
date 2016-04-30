(function( $ ) {
	'use strict';

	$(function() {

		$( document ).on( 'click', '.js-pht-events-calendar-link', function( event ) {

			event.preventDefault();
			var month = $(this).attr( 'data-calendar-link-month' ),
				year = $(this).attr( 'data-calendar-link-year' ),
				$container = $(this).parents( '.js-calendar-container' ),
				$blocks = $container.find( '.pht-events-calendar__blocks' ),
				$nav = $container.find( '.pht-events-calendar__nav' );
			$blocks.addClass( 'pht-events-day-hidden' );
			$nav.addClass( 'pht-events-nav-hidden' );
			$.post(
				self.pht_events_scriptparams.ajaxURL, {
					action: 'pehaathemes_events_calendar',
					nonce: self.pht_events_scriptparams.nonce,
					month: month,
					year: year
				},
				function ( html ) {
					
					var blocks_html = $( $( html )[0] ).children().filter( '.pht-events-calendar__blocks' ).html(),
						nav_html = $( $( html )[1] ).filter( '.pht-events-calendar__nav' ).html();
					
					setTimeout( function(){
						$blocks.html(blocks_html);
						$nav.html(nav_html);
					}, 250 );
					setTimeout( function(){
						$blocks.removeClass( 'pht-events-day-hidden' );
						$nav.removeClass( 'pht-events-nav-hidden' );
					}, 500);
					
					
				});
				return false;
			});
	});
	 
})( jQuery );