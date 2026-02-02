<template>
	<div class="ereader-read">
		<header v-if="!isMobile" class="ereader-read__header">
			<router-link :to="{ name: 'Library' }" class="ereader-read__back">{{ t('ereader', 'Back to library') }}</router-link>
		</header>
		<div class="ereader-read__area">
			<div v-if="dictMessage" class="ereader-read__toast">{{ dictMessage }}</div>
			<div v-if="error" class="ereader-read__error">{{ error }}</div>
			<div v-else-if="!isEpub && loadingPdf" class="ereader-read__loading">{{ t('ereader', 'Loading…') }}</div>
			<EpubViewer v-else-if="isEpub" :url="streamUrl" @add-to-dictionary="onAddToDictionary" />
			<PdfViewer v-else-if="pdfBlob" :blob="pdfBlob" @add-to-dictionary="onAddToDictionary" />
		</div>
	</div>
</template>

<script>
import { getStreamUrl, getStreamBlob } from '../router/api'
import { translate as t } from '@nextcloud/l10n'
import EpubViewer from '../components/EpubViewer.vue'
import PdfViewer from '../components/PdfViewer.vue'

const MIME_EPUB = 'application/epub+zip'
const MOBILE_BREAKPOINT = 768

export default {
	name: 'Read',
	components: { EpubViewer, PdfViewer },
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
			pdfBlob: null,
			windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1024,
			dictMessage: '',
		}
	},
	computed: {
		streamUrl() {
			return getStreamUrl(Number(this.fileId))
		},
		isEpub() {
			return this.$route.query?.mime === MIME_EPUB
		},
		isMobile() {
			return this.windowWidth <= MOBILE_BREAKPOINT
		},
	},
	async mounted() {
		window.addEventListener('resize', this.onResize)
		if (this.isEpub) return
		this.loadingPdf = true
		this.error = ''
		try {
			this.pdfBlob = await getStreamBlob(Number(this.fileId))
		} catch (e) {
			this.error = e.response?.status === 403
				? t('ereader', 'Access denied. Please refresh the page and try again.')
				: (e.message || t('ereader', 'Failed to load the file.'))
		} finally {
			this.loadingPdf = false
		}
	},
	beforeUnmount() {
		window.removeEventListener('resize', this.onResize)
		this.pdfBlob = null
	},
	methods: {
		t,
		onResize() {
			this.windowWidth = window.innerWidth
		},
	},
}
</script>

<style scoped>
.ereader-read {
	display: flex;
	flex-direction: column;
	height: 100%;
	min-height: 0;
	width: 100%;
	max-width: 100vw;
	box-sizing: border-box;
	overflow: hidden;
}
.ereader-read__header {
	flex-shrink: 0;
	padding: 0.5rem 1rem;
	background: var(--ereader-action-bar);
	color: var(--ereader-primary-text);
	border-bottom: 1px solid var(--ereader-border);
}
.ereader-read__back {
	color: inherit;
	text-decoration: none;
	font-weight: 500;
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
.ereader-read__error,
.ereader-read__loading {
	position: absolute;
	inset: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 2rem;
	text-align: center;
	color: var(--ereader-text-secondary);
}
.ereader-read__error {
	color: var(--ereader-error);
}
.ereader-read__toast {
	position: absolute;
	top: 1rem;
	left: 50%;
	transform: translateX(-50%);
	z-index: 50;
	padding: 0.5rem 1rem;
	background: var(--ereader-primary);
	color: var(--ereader-primary-text);
	border-radius: var(--ereader-radius-sm);
	font-size: 0.9rem;
	box-shadow: var(--ereader-card-shadow);
}
</style>
