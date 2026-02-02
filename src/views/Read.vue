<template>
	<div class="ereader-read">
		<header class="ereader-read__header">
			<router-link :to="{ name: 'Library' }" class="ereader-read__back">{{ t('ereader', 'Back to library') }}</router-link>
		</header>
		<div class="ereader-read__area">
			<div v-if="error" class="ereader-read__error">{{ error }}</div>
			<div v-else-if="!isEpub && loadingPdf" class="ereader-read__loading">{{ t('ereader', 'Loading…') }}</div>
			<EpubViewer v-else-if="isEpub" :url="streamUrl" />
			<iframe v-else-if="pdfBlobUrl" :src="pdfBlobUrl" class="ereader-read__iframe" title="Book content"></iframe>
		</div>
	</div>
</template>

<script>
import { getStreamUrl, getStreamBlob } from '../router/api'
import { translate as t } from '@nextcloud/l10n'
import EpubViewer from '../components/EpubViewer.vue'

const MIME_EPUB = 'application/epub+zip'

export default {
	name: 'Read',
	components: { EpubViewer },
	props: {
		fileId: {
			type: [String, Number],
			required: true,
		},
	},
	data() {
		return {
			error: '',
			loadingPdf: false,
			pdfBlobUrl: null,
		}
	},
	computed: {
		streamUrl() {
			return getStreamUrl(Number(this.fileId))
		},
		isEpub() {
			return this.$route.query?.mime === MIME_EPUB
		},
	},
	async mounted() {
		if (this.isEpub) return
		this.loadingPdf = true
		this.error = ''
		try {
			const blob = await getStreamBlob(Number(this.fileId))
			this.pdfBlobUrl = URL.createObjectURL(blob)
		} catch (e) {
			this.error = e.response?.status === 403
				? this.t('ereader', 'Access denied. Please refresh the page and try again.')
				: (e.message || this.t('ereader', 'Failed to load the file.'))
		} finally {
			this.loadingPdf = false
		}
	},
	beforeUnmount() {
		if (this.pdfBlobUrl) {
			URL.revokeObjectURL(this.pdfBlobUrl)
			this.pdfBlobUrl = null
		}
	},
	methods: {
		t,
	},
}
</script>

<style scoped>
.ereader-read {
	display: flex;
	flex-direction: column;
	height: 100vh;
	min-height: 100vh;
	width: 100%;
	max-width: 100vw;
	box-sizing: border-box;
	overflow: hidden;
}
.ereader-read__header {
	flex-shrink: 0;
	padding: 0.5rem 1rem;
	background: var(--color-background-hover, #f5f5f5);
	border-bottom: 1px solid var(--color-border, #ddd);
}
.ereader-read__back {
	color: var(--color-primary, #0082c9);
	text-decoration: none;
}
.ereader-read__back:hover {
	text-decoration: underline;
}
.ereader-read__area {
	flex: 1;
	position: relative;
	min-height: 0;
	width: 100%;
	overflow: hidden;
}
.ereader-read__iframe {
	position: absolute;
	inset: 0;
	width: 100%;
	height: 100%;
	min-width: 100%;
	min-height: 100%;
	border: none;
}
.ereader-read__error,
.ereader-read__loading {
	position: absolute;
	inset: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 2rem;
	text-align: center;
	color: var(--color-text-lighter, #666);
}
.ereader-read__error {
	color: var(--color-error, #c00);
}
</style>
