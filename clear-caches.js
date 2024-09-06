jQuery(document).ready($ => {
    // Flag to prevent multiple modals and AJAX requests
    let isProcessing = false;

    // Function to handle cache clearing via AJAX
    const clearCache = type => {
        if (isProcessing) {
            return; // Prevent action if already processing
        }

        // Set processing flag
        isProcessing = true;
        showModal('Processing...'); // Show modal immediately

        $.ajax({
            url: clearCachesData.ajaxurl,
            type: 'POST',
            data: {
                action: 'clear_caches_action',
                cache_type: type,
                security: clearCachesData.nonce
            },
            success: response => {
                const message = response.success ? response.data.message : 'Error: ' + response.data.message;
                updateModal(message, response.success ? 'success' : 'error'); // Update modal with final message
            },
            error: () => {
                updateModal('An error occurred while clearing the cache.', 'error'); // Update modal with error message
            },
            complete: () => {
                // Reset the processing flag after the request completes
                isProcessing = false;

                // Automatically hide the modal after 2 seconds once the final message is shown
                setTimeout(() => {
                    $('#cache-clear-modal, #cache-clear-overlay').remove();
                }, 2000);
            }
        });
    };

    // Assign click events to all cache links
    $('.clear-cache-php-opcache').on('click', e => handleClick(e, 'php_opcache'));
    $('.clear-cache-nginx').on('click', e => handleClick(e, 'nginx_cache'));
    $('.clear-cache-object').on('click', e => handleClick(e, 'object_cache'));
    $('.clear-cache-transients').on('click', e => handleClick(e, 'clear_transients'));

    // Handle click event and call clearCache
    const handleClick = (e, cacheType) => {
        e.preventDefault();
        if (!isProcessing) {
            clearCache(cacheType);
        }
    };

    // Function to display the initial modal with the "Processing..." message
    function showModal(message) {
        // Create overlay if not exists
        if ($('#cache-clear-overlay').length === 0) {
            $('<div id="cache-clear-overlay"></div>').css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0, 0, 0, 0.7)', // Darker background
                zIndex: 99999, // Ensure the highest z-index
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center'
            }).appendTo('body');
        }

        // Create modal if not exists
        if ($('#cache-clear-modal').length === 0) {
            $('<div id="cache-clear-modal"></div>').css({
                backgroundColor: '#fff',
                padding: '20px',
                textAlign: 'center',
                fontSize: '16px',
                color: '#333',
                lineHeight: '1.5',
                zIndex: 100000, // Ensure the modal is on top of everything
                minWidth: '300px',
                maxWidth: '80%'
            }).appendTo('#cache-clear-overlay');
        }

        $('#cache-clear-modal').html('<p>' + message + '</p>');
    }

    // Function to update the modal with the final message (success or error)
    function updateModal(message, type) {
        $('#cache-clear-modal').html('<p>' + message + '</p>');
    }
});

// Ref: ChatGPT
