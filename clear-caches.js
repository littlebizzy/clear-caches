jQuery(document).ready($ => {
    // Flag to prevent multiple modals and AJAX requests
    let isProcessing = false;

    // Function to handle cache clearing via AJAX
    const clearCache = type => {
        if (isProcessing) {
            return; // Prevent action if already processing
        }

        // Set processing flag and disable all links
        isProcessing = true;
        toggleLinksState(true); // Disable all links

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
                showModal(message, response.success ? 'success' : 'error');
            },
            error: () => {
                showModal('An error occurred while clearing the cache.', 'error');
            },
            complete: () => {
                // Re-enable all links after 2 seconds
                setTimeout(() => {
                    isProcessing = false;
                    toggleLinksState(false); // Enable all links
                }, 2000);
            }
        });
    };

    // Toggle the enabled/disabled state of all cache links
    const toggleLinksState = disable => {
        const action = disable ? 'addClass' : 'removeClass';
        $('.clear-cache-php-opcache, .clear-cache-nginx, .clear-cache-object, .clear-cache-transients')[action]('disabled');
    };

    // Assign click events to all cache links
    $('.clear-cache-php-opcache').on('click', e => handleClick(e, 'php_opcache'));
    $('.clear-cache-nginx').on('click', e => handleClick(e, 'nginx_cache'));
    $('.clear-cache-object').on('click', e => handleClick(e, 'object_cache'));
    $('.clear-cache-transients').on('click', e => handleClick(e, 'clear_transients'));

    // Handle click event and call clearCache
    const handleClick = (e, cacheType) => {
        e.preventDefault();
        if (!$(e.currentTarget).hasClass('disabled')) {
            clearCache(cacheType);
        }
    };

    // Function to display a modal with a message
    function showModal(message, type) {
        // Create overlay if not exists
        if ($('#cache-clear-overlay').length === 0) {
            $('<div id="cache-clear-overlay"></div>').css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0, 0, 0, 0.5)',
                zIndex: 9998
            }).appendTo('body');
        }

        // Create modal if not exists
        if ($('#cache-clear-modal').length === 0) {
            $('<div id="cache-clear-modal"></div>').css({
                position: 'fixed',
                top: '50%',
                left: '50%',
                transform: 'translate(-50%, -50%)',
                backgroundColor: '#fff',
                padding: '20px',
                zIndex: 9999,
                width: '300px',
                maxWidth: '80%',
                textAlign: 'center',
                fontSize: '16px',
                color: '#333',
                lineHeight: '1.5',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                minHeight: '100px'
            }).appendTo('body');
        }

        $('#cache-clear-modal').html('<p>' + message + '</p>');

        // Automatically hide modal and overlay after 2 seconds
        setTimeout(() => {
            $('#cache-clear-modal, #cache-clear-overlay').remove();
        }, 2000);
    }
});

// Ref: ChatGPT
