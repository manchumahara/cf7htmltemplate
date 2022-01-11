(function ($) {
	'use strict';

	$(document).ready(function () {
		$('.spectrum_color').spectrum({
			showAlpha: false,
			showInput: true,
			showPalette: true,
			preferredFormat: 'hex',
			cancelText: cf7htmltemplate.spectrum.cancelText,
			chooseText: cf7htmltemplate.spectrum.chooseText,
			togglePaletteMoreText: cf7htmltemplate.spectrum.togglePaletteMoreText,
			togglePaletteLessText: cf7htmltemplate.spectrum.togglePaletteLessText,
			clearText: cf7htmltemplate.spectrum.clearText,
			noColorSelectedText: cf7htmltemplate.spectrum.noColorSelectedText,
		});

		$('.cf7htmltemplate-photo').on('click', function (event) {
			event.preventDefault();

			var self = $(this);

			// Create the media frame.
			var file_frame = wp.media.frames.file_frame = wp.media({
				title   : cf7htmltemplate.upload_title,
				button  : {
					text: cf7htmltemplate.please_select
				},
				multiple: false,
				library: {
					type: [ 'image' ]
				}
			});

			file_frame.on('select', function () {
				var attachment = file_frame.state().get('selection').first().toJSON();

				self.prev('.cf7htmltemplate-photo-url').val(attachment.url);
			});

			// Finally, open the modal
			file_frame.open();
		});
	});
})(jQuery);