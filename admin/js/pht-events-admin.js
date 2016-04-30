(function( $ ) {
	'use strict';
	$(function() { 
		$('.js-pht-event-date').datetimepicker({timepicker:false, format:'Y-m-d'});
		$('.js-pht-event-time').datetimepicker({datepicker:false, format:'H:i',});
	});
})( jQuery );
