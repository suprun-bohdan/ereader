<template>
	<div class="pdf-viewer">
		<div ref="containerEl" class="pdf-viewer__container">
			<canvas
				v-for="pageNum in numPages"
				:key="pageNum"
				:ref="(el) => setCanvasRef(el, pageNum)"
				class="pdf-viewer__page"
			/>
		</div>
		<div v-if="loading" class="pdf-viewer__loading">{{ t('ereader', 'Loading…') }}</div>
		<div v-else-if="error" class="pdf-viewer__error">{{ error }}</div>
	</div>
</template>

<script>
import { translate as t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import * as pdfjsLib from 'pdfjs-dist'

// Point worker to our built file (Nextcloud app js path)
if (typeof window !== 'undefined' && !pdfjsLib.GlobalWorkerOptions.workerSrc) {
	pdfjsLib.GlobalWorkerOptions.workerSrc = generateUrl('/apps/ereader/js/pdf.worker.js')
}

export default {
	name: 'PdfViewer',
	props: {
		blob: {
			type: Blob,
			required: true,
		},
	},
	data() {
		return {
			loading: true,
			error: '',
			numPages: 0,
			pdfDoc: null,
			canvasRefs: {},
		}
	},
	async mounted() {
		await this.loadPdf()
	},
	beforeUnmount() {
		this.canvasRefs = {}
		if (this.pdfDoc) {
			this.pdfDoc.destroy()
			this.pdfDoc = null
		}
	},
	methods: {
		t,
		setCanvasRef(el, pageNum) {
			if (el) this.canvasRefs[pageNum] = el
		},
		async loadPdf() {
			this.loading = true
			this.error = ''
			try {
				const data = await this.blob.arrayBuffer()
				this.pdfDoc = await pdfjsLib.getDocument({ data }).promise
				this.numPages = this.pdfDoc.numPages
				this.loading = false
				await this.$nextTick()
				await this.renderAllPages()
			} catch (e) {
				this.error = e.message || t('ereader', 'Failed to load PDF')
				this.loading = false
			}
		},
		async renderAllPages() {
			if (!this.pdfDoc || !this.$refs.containerEl) return
			const container = this.$refs.containerEl
			const width = container.clientWidth || 800
			for (let n = 1; n <= this.numPages; n++) {
				const canvas = this.canvasRefs[n]
				if (!canvas) continue
				const page = await this.pdfDoc.getPage(n)
				const scale = width / page.getViewport({ scale: 1 }).width
				const viewport = page.getViewport({ scale })
				canvas.height = viewport.height
				canvas.width = viewport.width
				const ctx = canvas.getContext('2d')
				await page.render({
					canvasContext: ctx,
					viewport,
				}).promise
			}
		},
	},
}
</script>

<style scoped>
.pdf-viewer {
	position: relative;
	width: 100%;
	height: 100%;
	background: var(--ereader-surface);
	overflow: auto;
}

.pdf-viewer__container {
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 1rem 0;
	gap: 1rem;
}

.pdf-viewer__page {
	display: block;
	max-width: 100%;
	height: auto;
	box-shadow: var(--ereader-card-shadow);
}

.pdf-viewer__loading,
.pdf-viewer__error {
	position: absolute;
	inset: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 2rem;
	text-align: center;
	color: var(--ereader-text-secondary);
}

.pdf-viewer__error {
	color: var(--ereader-error);
}
</style>
