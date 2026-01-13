;(function($){
	$( document ).ready( function(){
		//- container
		// -- row[i]
		// --- control[i][q]
		var reindexRows = function( $container ) {
			$container.find('.js-queulat-repeater__row').each( function( i, row ){
				$( row ).data( 'row', i );
				$( row ).attr( 'data-row', i );
				reindexRowControls( i, $( row ) );
			});
		};
		var reindexRowControls = function( index, $row ) {
			var controlsSelector = [
				'.js-queulat-repeater__control',
				'.js-queulat-repeater__control input'
			];
			return $row.find( controlsSelector.join(',') ).each( function( i, obj ){
				var rowIndex     = index;
				var nameTemplate = $( obj ).data('name') || $( obj ).closest('.js-queulat-repeater__control').data('name');
				var newName      = nameTemplate.replace('__i__', rowIndex );
				$( obj ).attr( 'name', newName );
				$( obj ).data( 'row', rowIndex );
				$( obj ).attr( 'data-row', rowIndex );
				if ( $( obj ).hasClass('js-queulat-wp-media')  ) {
					var $itemTemplate = $( obj ).find('.tmpl-wpmedia-item');
					var regex = /name=".+?"/
					var newItemTemplate = $itemTemplate.html().replace( regex, 'name=\"'+newName+'\"');
					$itemTemplate.html( newItemTemplate );
				}
			} );
		};
		$( 'body' ).on( 'click', '.js-queulat-repeater__add', function( event ){
			event.preventDefault();
			var $container   = $( this ).closest('.js-queulat-repeater');
			var $rows        = $container.find('.js-queulat-repeater__row');
			var $lastRow     = $rows.filter(':last');
			var lastRowIndex = parseInt( $lastRow.data('row'), 10 );
			var clonedRow    = $lastRow[0].outerHTML;
			var regex        = new RegExp('(\\[[^\\]]+\\])\\[' + lastRowIndex + '\\](\\[[^\\]]+\\])', 'gm');
			var newClonedRow = clonedRow.replaceAll( regex, '$1\['+(lastRowIndex+1)+'\]$2' );
			$lastRow.after( newClonedRow );

			var $clonedRow = $container.find('.js-queulat-repeater__row').filter(':last');
			$clonedRow.find('div.queulat-wpmedia-item').remove();
			$clonedRow.find('input:not([type="radio"],[type="checkbox"]), select').val('');
			$clonedRow.find('input, select').filter(':first').trigger('focus');
			reindexRows( $container );
		} ).on( 'click', '.js-queulat-repeater__remove', function( event ){
			var $container = $(this).closest('.js-queulat-repeater');
			var $rows      = $container.find('.js-queulat-repeater__row');
			if ( $rows.length > 1 ) {
				$( this ).closest( '.js-queulat-repeater__row' ).remove();
				reindexRows( $container );
			}
			event.preventDefault();
		} ).on( 'click', '.js-queulat-repeater__up', function( event ){
			event.preventDefault();
			var $row = $( this ).closest( '.js-queulat-repeater__row' );
			if ( parseInt( $row.data('row'), 10 ) === 0 ) {
				return false;
			}
			var $clonedRow = $row.clone();
			var $prev = $row.prev();
			$prev.before( $clonedRow );
			$row.remove();
			reindexRows( $clonedRow.closest('.js-queulat-repeater') );
		} ).on( 'click', '.js-queulat-repeater__down', function( event ){
			event.preventDefault();
			var $row = $( this ).closest( '.js-queulat-repeater__row' );
			if ( parseInt( $row.data('row'), 10 ) === $( this ).closest( '.js-queulat-repeater').find( '.js-queulat-repeater__row').length - 1 ) {
				return false;
			}
			var $clonedRow = $row.clone();
			var $next = $row.next();
			$next.after( $clonedRow );
			$row.remove();
			reindexRows( $clonedRow.closest('.js-queulat-repeater') );
		} );
	} );
})(jQuery);
