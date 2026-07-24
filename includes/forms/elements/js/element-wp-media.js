/* global jQuery, _ */
(function ($) {
	$(document)
		.on('click', 'button.queulat-wpmedia-upload', function (event) {
			// eslint-disable-next-line prefer-const
			let fileFrame;

			// props to @mikejolley and @hugosolar for this
			// @see https://mikejolley.com/2012/12/21/using-the-new-wordpress-3-5-media-uploader-in-plugins/
			event.preventDefault();

			// If the media frame already exists, reopen it.
			if (fileFrame) {
				fileFrame.open();
				return;
			}

			const $container = $(this).closest('.js-queulat-wp-media');

			const args = $.extend(
				true,
				{
					title: '',
					multiple: false,
					button: {
						text: '',
					},
				},
				$container.data('wpmedia-args')
			);

			// Create the media frame.
			fileFrame = wp.media(args);

			// When opening the media library, check and pre-select existing values
			fileFrame.on('open', function () {
				const selection = fileFrame.state().get('selection');
				$container
					.find('input.queulat-wpmedia-value')
					.each(function (i, obj) {
						const id = parseInt(obj.value, 10),
							attachment = wp.media.model.Attachment.get(id);
						selection.add(attachment);
					});
			});

			const itemTemplate = _.template(
				$container.find('.tmpl-wpmedia-item').html()
			);
			const renderItems = function (items) {
				let selectedItems = '';
				items.forEach(function (item) {
					selectedItems += itemTemplate(item);
				});
				return selectedItems;
			};

			const $receiver = $container.find('div.queulat-wpmedia-receiver');
			// When an image is selected, run a callback.
			fileFrame.on('select', function () {
				const selection = fileFrame.state().get('selection');
				$receiver.html(renderItems(selection));
			});

			fileFrame.open();
		})
		.on('click', 'button.queulat-wpmedia-item-remove', function (event) {
			event.preventDefault();
			$(this)
				.closest('div.queulat-wpmedia-item')
				.fadeOut('fast', function () {
					$(this).remove();
				});
		})
		.ready(function () {
			$('.js-queulat-wp-media').each(function () {
				const values = $(this).data('wpmedia-value'),
					itemTemplate = _.template(
						$(this).find('.tmpl-wpmedia-item').html()
					),
					$receiver = $(this).find('div.queulat-wpmedia-receiver');
				let selected = '';
				if (!_.isEmpty(values)) {
					values.forEach(function (item) {
						if (item) {
							selected += itemTemplate({ attributes: item });
						}
					});
				}
				$receiver.html(selected);
				$(this).find('.queulat-wpmedia-sortable').sortable({
					containment: 'parent',
					items: '.queulat-wpmedia-sortable-item',
					forcePlaceholerSize: true,
				});
			});
		});
})(jQuery);
