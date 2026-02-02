/**
 * Register "Open in e-reader" file action for EPUB and PDF in the Files app.
 */
import { FileAction, registerFileAction } from '@nextcloud/files'
import { translate as t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'

const MIME_EPUB = 'application/epub+zip'
const MIME_PDF = 'application/pdf'

const action = new FileAction({
	id: 'ereader-open',
	displayName: () => t('ereader', 'Open in e-reader'),
	title: () => t('ereader', 'Open in e-reader'),
	iconSvgInline: () => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
	enabled: (files) => files.length > 0 && files.every(f => f.mime === MIME_EPUB || f.mime === MIME_PDF),
	exec: async (file) => {
		const fileId = file.fileid
		if (fileId == null) return false
		const mime = file.mime || ''
		const url = generateUrl('/apps/ereader/') + '#/read/' + fileId + '?mime=' + encodeURIComponent(mime)
		window.location.href = url
		return true
	},
	order: 100,
})

registerFileAction(action)
