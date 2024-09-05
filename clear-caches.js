jQuery(document).ready(($) => {
    // Assign click events using event delegation with modern syntax
    $('body').on('click', '.clear-cache-php-opcache', (e) => {
        e.preventDefault();  // Prevent default link action
        clearCache('php_opcache'); // Pass the cache type as an argument
    });

    $('body').on('click', '.clear-cache-nginx', (e) => {
        e.preventDefault();  // Prevent default link action
        clearCache('nginx_cache'); // Pass the cache type as an argument
    });

    $('body').on('click', '.clear-cache-object', (e) => {
        e.preventDefault();  // Prevent default link action
        clearCache('object_cache'); // Pass the cache type as an argument
    });

    $('body').on('click', '.clear-cache-transients', (e) => {
        e.preventDefault();  // Prevent default link action
        clearCache('clear_transients'); // Pass the cache type as an argument
    });

    // Function to handle cache clearing via AJAX
    const clearCache = (type) => {
        showModal('Processing...', 'info');  // Show modal immediately upon click

        $.ajax({
            url: clearCachesData.ajaxurl, // Use localized script variable for AJAX URL
            type: 'POST',
            data: {
                action: 'clear_caches_action', // Correct action name for WordPress
                cache_type: type, // Send the cache type to the server-side PHP
                security: clearCachesData.nonce // Security nonce for verification
            },
            success: (response) => {
                if (response.success) {
                    showModal(response.data.message, 'success');
                } else {
                    showModal('Error: ' + response.data.message, 'error');
                }
            },
            error: (xhr, status, error) => {
                console.error('Error: ', xhr, status, error);
                showModal('An error occurred while clearing the cache.', 'error');
            }
        });
    };

    // Function to display a modal with a message
    const showModal = (message, type) => {
        // Create overlay and modal elements
        const overlay = $('<div id="cache-clear-overlay"></div>').css({
            'position': 'fixed',
            'top': 0,
            'left': 0,
            'width': '100%',
            'height': '100%',
            'background-color': 'rgba(0, 0, 0, 0.5)', // Transparent gray background
            'z-index': 9998
        });

        const modal = $('<div id="cache-clear-modal" role="alert"></div>').css({
            'position': 'fixed',
            'top': '50%',
            'left': '50%',
            'transform': 'translate(-50%, -50%)',
            'background-color': '#fff',
            'padding': '20px',
            'z-index': 9999,
            'width': '300px',  // Static width for desktop
            'max-width': '80%',  // Responsive width for mobile
            'text-align': 'center',
            'font-size': '16px',
            'color': '#333',
            'line-height': '1.5',
            'display': 'flex',
            'align-items': 'center',
            'justify-content': 'center',
            'min-height': '100px'
        });

        // Adding message content with consistent styling
        const messageContent = $('<p></p>').text(message).css({
            'margin': '0',
            'padding': '0'
        });

        modal.append(messageContent);

        // Append elements to body
        $('body').append(overlay).append(modal);

        // Automatically hide modal and overlay after 2 seconds
        setTimeout(() => {
            $('#cache-clear-modal, #cache-clear-overlay').remove();
        }, 2000);
    };
});

// Ref: ChatGPT
