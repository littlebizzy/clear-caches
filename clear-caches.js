jQuery( document ).ready( $ => {
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
					$( '#cache-clear-modal, #cache-clear-overlay' ).remove();
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
		if ( $( '#cache-clear-overlay' ).length === 0 ) {
			$( '<div id="cache-clear-overlay"></div>' ).css( {
				position: 'fixed',
				top: 0,
				left: 0,
				width: '100%',
				height: '100%',
				backgroundColor: 'rgba(0, 0, 0, 0.7)',
				zIndex: 99999,
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center'
			} ).appendTo( 'body' );
		}

		if ( $( '#cache-clear-modal' ).length === 0 ) {
			$( '<div id="cache-clear-modal"></div>' ).css( {
				backgroundColor: '#fff',
				padding: '20px',
				textAlign: 'center',
				fontSize: '16px',
				color: '#333',
				lineHeight: '1.5',
				zIndex: 100000,
				minWidth: '300px',
				maxWidth: '80%'
			} ).appendTo( '#cache-clear-overlay' );
		}

		$( '#cache-clear-modal' ).html( '<p>' + message + '</p>' );
	}

	// update modal content
	function updateModal( message, type ) {
		$( '#cache-clear-modal' ).html( '<p>' + message + '</p>' );
	}
} );

// Ref: ChatGPT
