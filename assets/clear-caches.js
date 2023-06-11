/* global clearCachesData */
window.ClearCaches = ($ => {
	const ClearCaches = function () {
		this.purge = (event, cacheType) => {
			event.preventDefault()

			const links = Array.from(document.querySelector('.clear-caches-links').getElementsByTagName('a'))
			links.map(link => {
				link.style.pointerEvents = 'none'
			})

			$.post(
				clearCachesData.ajaxUrl,
				{action: 'clear_caches_purge', cacheType, _wpnonce: clearCachesData.nonce},
				response => {
					if (response.success) {
						alert(event.target.text + ' done successfully!');
					} else {
						alert(response.data);
					}

					links.map(link => {
						link.style.pointerEvents = null
					})
				}
			)
		}
	}

	return new ClearCaches()
})(jQuery)
