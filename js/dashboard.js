/**
 * e-reader dashboard widget: book shelf with recent books (inspired by eReader app widget).
 */
(function() {
	'use strict';

	function loadState(app, key, fallback) {
		fallback = fallback !== undefined ? fallback : null;
		try {
			if (typeof OCP !== 'undefined' && OCP.InitialState && typeof OCP.InitialState.loadState === 'function') {
				return OCP.InitialState.loadState(app, key);
			}
			if (typeof OC !== 'undefined' && OC.InitialState && typeof OC.InitialState.loadState === 'function') {
				return OC.InitialState.loadState(app, key);
			}
		} catch (e) {}
		return fallback;
	}

	document.addEventListener('DOMContentLoaded', function() {
		if (typeof OCA === 'undefined' || !OCA.Dashboard || typeof OCA.Dashboard.register !== 'function') {
			return;
		}

		OCA.Dashboard.register('ereader_widget', function(el) {
			if (!el || !el.querySelector) return;

			var data = loadState('ereader', 'dashboardData', { recentBooks: [], appUrl: '#' });
			var recentBooks = (data && Array.isArray(data.recentBooks)) ? data.recentBooks : [];
			var appUrl = (data && data.appUrl) ? data.appUrl : '#';

			el.innerHTML = '';
			el.classList.add('ereader-widget');

			var shelf = document.createElement('div');
			shelf.className = 'ereader-widget__shelf';

			if (recentBooks.length > 0) {
				recentBooks.forEach(function(book) {
					var link = document.createElement('a');
					link.className = 'ereader-widget__book';
					link.href = book.readUrl || (appUrl + '#/read/' + book.fileId);
					link.setAttribute('aria-label', book.title || 'Open book');

					var spine = document.createElement('div');
					spine.className = 'ereader-widget__book-spine';
					spine.setAttribute('aria-hidden', 'true');
					spine.textContent = '\uD83D\uDCD6'; /* 📖 open book */

					var title = document.createElement('span');
					title.className = 'ereader-widget__book-title';
					title.textContent = book.title || '';

					link.appendChild(spine);
					link.appendChild(title);
					shelf.appendChild(link);
				});
			} else {
				/* Placeholder books when no recent books */
				var placeholders = 4;
				for (var i = 0; i < placeholders; i++) {
					var book = document.createElement('div');
					book.className = 'ereader-widget__book ereader-widget__book--placeholder';

					var spine = document.createElement('div');
					spine.className = 'ereader-widget__book-spine';
					spine.setAttribute('aria-hidden', 'true');
					spine.textContent = '\uD83D\uDCDA'; /* 📚 books */

					book.appendChild(spine);
					shelf.appendChild(book);
				}
			}

			el.appendChild(shelf);

			var openLabel = 'Open e-reader';
			try {
				if (typeof OC !== 'undefined' && OC.L10N && OC.L10N.translate) {
					openLabel = OC.L10N.translate('ereader', 'Open e-reader');
				} else if (typeof t === 'function') {
					openLabel = t('ereader', 'Open e-reader');
				}
			} catch (e) {}
			var openLink = document.createElement('a');
			openLink.className = 'ereader-widget__open';
			openLink.href = appUrl;
			openLink.textContent = openLabel;
			openLink.setAttribute('aria-label', openLabel);
			el.appendChild(openLink);
		});
	});
})();
