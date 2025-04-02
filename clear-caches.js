jQuery( document ).ready( $ => {
	// exit if ajax data is not available
	if ( typeof clearCachesData === 'undefined' ) return;

	// prevent multiple ajax requests
	let isProcessing = false;

	// send ajax request to clear cache
	const clearCache = ( type ) => {
		if ( isProcessing ) return;

		isProcessing = true;
		showModal( 'Please wait...' );

		$.ajax( {
			url: clearCachesData.ajaxurl,
			type: 'POST',
			data: {
				action: 'clear_caches_action',
				cache_type: type,
				security: clearCachesData.nonce
			},
			success: ( response ) => {
				const message = response?.data?.message || 'Unexpected server response.';
				const status = response.success ? 'success' : 'error';
				updateModal( message, status );
			},
			error: () => {
				updateModal( 'Request failed. Please try again.', 'error' );
			},
			complete: () => {
				isProcessing = false;
				setTimeout( () => {
					$( '#clear-caches-modal, #clear-caches-overlay' ).remove();
				}, 2000 );
			}
		} );
	};

	// attach click events
	$( '.clear-cache-php-opcache' ).on( 'click', ( e ) => handleClick( e, 'php_opcache' ) );
	$( '.clear-cache-nginx' ).on( 'click', ( e ) => handleClick( e, 'nginx_cache' ) );
	$( '.clear-cache-object' ).on( 'click', ( e ) => handleClick( e, 'object_cache' ) );
	$( '.clear-cache-transients' ).on( 'click', ( e ) => handleClick( e, 'clear_transients' ) );

	// trigger ajax on click
	const handleClick = ( e, cacheType ) => {
		e.preventDefault();
		if ( ! isProcessing ) {
			clearCache( cacheType );
		}
	};

	// show modal overlay with message
	function showModal( message ) {
		if ( $( '#clear-caches-overlay' ).length === 0 ) {
			$( '<div id="clear-caches-overlay"></div>' ).css( {
				position: 'fixed',
				top: 0,
				left: 0,
				width: '100%',
				height: '100%',
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center',
				backgroundColor: 'rgba(0, 0, 0, 0.7)',
				zIndex: 99999
			} ).appendTo( 'body' );
		}

		if ( $( '#clear-caches-modal' ).length === 0 ) {
			$( '<div id="clear-caches-modal" role="alertdialog" aria-live="assertive" aria-modal="true"></div>' ).css( {
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center',
				flexDirection: 'column',
				width: '320px',
				maxWidth: '90%',
				minHeight: '60px',
				overflowY: 'auto',
				padding: '16px 24px',
				backgroundColor: '#fff',
				color: '#23282d',
				fontSize: '13px',
				fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif',
				textAlign: 'center',
				lineHeight: '1.5',
				borderRadius: '4px',
				boxShadow: '0 1px 3px rgba(0, 0, 0, 0.1)',
				zIndex: 100000
			} ).appendTo( '#clear-caches-overlay' );
		}

		$( '#clear-caches-modal' ).text( message );
	}

	// update modal content
	function updateModal( message, type ) {
		$( '#clear-caches-modal' ).text( message );
	}
} );

// Ref: ChatGPT
