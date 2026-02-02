/**
 * Dashboard widget for e-reader app.
 * Registers the widget UI so the dashboard can render it; the widget title links to the app.
 */
(function() {
	'use strict';

	document.addEventListener('DOMContentLoaded', function() {
		if (typeof OCA !== 'undefined' && OCA.Dashboard && typeof OCA.Dashboard.register === 'function') {
			OCA.Dashboard.register('ereader_widget', function(el) {
				if (!el || !el.querySelector) return;
				// Widget content: simple link to open the e-reader app (URL is set by PHP getUrl())
				var link = el.querySelector('a[href]');
				if (link) {
					link.setAttribute('aria-label', link.textContent || 'e-reader');
				}
			});
		}
	});
})();
