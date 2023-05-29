/* global clearCachesData */
window.ClearCaches = ($ => {
	const ClearCaches = function () {

	}

	ClearCaches.prototype.purgeAll = () => {
		console.log(clearCachesData.clearCachesNonce, 'purgeAll')
	}

	ClearCaches.prototype.purgeNginx = () => {
		console.log(clearCachesData.clearCachesNginxNonce, 'purgeNginx')
	}

	ClearCaches.prototype.purgeOPcache = () => {
		console.log(clearCachesData.clearCachesOpcache, 'purgeOPcache')
	}

	ClearCaches.prototype.purgeObject = () => {
		console.log(clearCachesData.clearCachesObjectNonce, 'purgeObject')
	}

	return new ClearCaches()
})(jQuery)
