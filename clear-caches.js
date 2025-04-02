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
				backgroundColor: 'rgba(0, 0, 0, 0.6)',
				zIndex: 99999,
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center'
			} ).appendTo( 'body' );
		}

		if ( $( '#clear-caches-modal' ).length === 0 ) {
			$( '<div id="clear-caches-modal"></div>' ).css( {
				backgroundColor: '#ffffff',
				padding: '24px 32px',
				textAlign: 'center',
				fontSize: '15px',
				color: '#222',
				lineHeight: '1.6',
				boxShadow: '0 6px 20px rgba(0, 0, 0, 0.15)',
				borderRadius: '8px',
				zIndex: 100000,
				minWidth: '280px',
				maxWidth: '90%',
				maxHeight: '80%',
				overflowY: 'auto'
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
