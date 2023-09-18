require([
	'jquery',
	'whatapi'
], function(jQuery){
	(function($) {

		$('#myDiv').floatingWhatsApp({
            phone: '593998067791',
            popupMessage: 'Hola, necesitas información sobre Outsourcing?',
            showPopup: true,
			position: 'right'
        });
		
	})(jQuery);
});