<template>
	<div class="pdf-viewer">
		<header v-if="!loading && !error" class="pdf-viewer__toolbar">
			<div class="pdf-viewer__toolbar-group">
				<button
					type="button"
					class="pdf-viewer__btn"
					:title="t('ereader', 'Zoom out')"
					:disabled="scaleFactor <= minScale"
					@click="zoomOut"
				>
					<svg class="pdf-viewer__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M19 13H5v-2h14v2z"/>
					</svg>
				</button>
				<span class="pdf-viewer__scale">{{ scalePercent }}%</span>
				<button
					type="button"
					class="pdf-viewer__btn"
					:title="t('ereader', 'Zoom in')"
					:disabled="scaleFactor >= maxScale"
					@click="zoomIn"
				>
					<svg class="pdf-viewer__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
					</svg>
				</button>
			</div>
			<button
				type="button"
				class="pdf-viewer__btn pdf-viewer__btn--text"
				:title="t('ereader', 'Fit to width')"
				@click="fitWidth"
			>
				{{ t('ereader', 'Fit width') }}
			</button>
			<button
				type="button"
				class="pdf-viewer__btn pdf-viewer__btn--text"
				:title="t('ereader', 'Fit page')"
				@click="fitPage"
			>
				{{ t('ereader', 'Fit page') }}
			</button>
			<div class="pdf-viewer__toolbar-group pdf-viewer__toolbar-group--page">
				<button
					type="button"
					class="pdf-viewer__btn"
					:title="t('ereader', 'Previous page')"
					:disabled="currentPage <= 1"
					@click="goToPage(currentPage - 1)"
				>
					<svg class="pdf-viewer__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
					</svg>
				</button>
				<span class="pdf-viewer__page-indicator">
					{{ t('ereader', 'Page') }} {{ currentPage }} / {{ numPages }}
				</span>
				<button
					type="button"
					class="pdf-viewer__btn"
					:title="t('ereader', 'Next page')"
					:disabled="currentPage >= numPages"
					@click="goToPage(currentPage + 1)"
				>
					<svg class="pdf-viewer__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
					</svg>
				</button>
			</div>
		</header>
		<div ref="scrollEl" class="pdf-viewer__scroll" @scroll="onScroll">
			<div ref="containerEl" class="pdf-viewer__container">
				<div
					v-for="pageNum in numPages"
					:key="pageNum"
					:ref="(el) => setPageWrapRef(el, pageNum)"
					class="pdf-viewer__page-wrap"
					:data-page="pageNum"
				>
					<canvas
						:ref="(el) => setCanvasRef(el, pageNum)"
						class="pdf-viewer__page"
					/>
				</div>
			</div>
		</div>
		<div v-if="loading" class="pdf-viewer__loading">{{ t('ereader', 'Loading…') }}</div>
		<div v-else-if="error" class="pdf-viewer__error">{{ error }}</div>
	</div>
</template>

<script>
import { markRaw } from 'vue'
import { translate as t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import * as pdfjsLib from 'pdfjs-dist'

if (typeof window !== 'undefined' && !pdfjsLib.GlobalWorkerOptions.workerSrc) {
	pdfjsLib.GlobalWorkerOptions.workerSrc = generateUrl('/apps/ereader/js/pdf.worker.js')
}

const MIN_SCALE = 0.5
const MAX_SCALE = 3
const ZOOM_STEP = 0.25

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
			pageWrapRefs: {},
			scaleFactor: 1,
			currentPage: 1,
			minScale: MIN_SCALE,
			maxScale: MAX_SCALE,
			pixelRatio: typeof window !== 'undefined' ? Math.min(window.devicePixelRatio || 1, 2) : 1,
		}
	},
	computed: {
		scalePercent() {
			return Math.round(this.scaleFactor * 100)
		},
	},
	async mounted() {
		await this.loadPdf()
	},
	beforeUnmount() {
		this.canvasRefs = {}
		this.pageWrapRefs = {}
		if (this.$refs.scrollEl) {
			this.$refs.scrollEl.removeEventListener('scroll', this.onScroll)
		}
		if (this.pdfDoc && typeof this.pdfDoc.destroy === 'function') {
			try {
				this.pdfDoc.destroy()
			} catch (_) {}
			this.pdfDoc = null
		}
	},
	methods: {
		t,
		setCanvasRef(el, pageNum) {
			if (el) this.canvasRefs[pageNum] = el
		},
		setPageWrapRef(el, pageNum) {
			if (el) this.pageWrapRefs[pageNum] = el
		},
		zoomIn() {
			this.scaleFactor = Math.min(this.maxScale, this.scaleFactor + ZOOM_STEP)
			this.$nextTick(() => this.renderAllPages())
		},
		zoomOut() {
			this.scaleFactor = Math.max(this.minScale, this.scaleFactor - ZOOM_STEP)
			this.$nextTick(() => this.renderAllPages())
		},
		fitWidth() {
			this.scaleFactor = 1
			this.$nextTick(() => this.renderAllPages())
		},
		fitPage() {
			this.scaleFactor = this.fitPageScale
			this.$nextTick(() => this.renderAllPages())
		},
		goToPage(pageNum) {
			const n = Math.max(1, Math.min(this.numPages, pageNum))
			this.currentPage = n
			this.$nextTick(() => {
				const el = this.pageWrapRefs[n]
				if (el && this.$refs.scrollEl) {
					el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' })
				}
			})
		},
		onScroll() {
			const scrollEl = this.$refs.scrollEl
			if (!scrollEl || this.numPages === 0) return
			const scrollLeft = scrollEl.scrollLeft
			const viewportCenter = scrollLeft + scrollEl.clientWidth / 2
			let page = 1
			let bestDist = Infinity
			for (let n = 1; n <= this.numPages; n++) {
				const wrap = this.pageWrapRefs[n]
				if (!wrap) continue
				const wrapCenter = wrap.offsetLeft + wrap.offsetWidth / 2
				const dist = Math.abs(viewportCenter - wrapCenter)
				if (dist < bestDist) {
					bestDist = dist
					page = n
				}
			}
			this.currentPage = page
		},
		async loadPdf() {
			this.loading = true
			this.error = ''
			try {
				const data = await this.blob.arrayBuffer()
				const doc = await pdfjsLib.getDocument({ data }).promise
				this.pdfDoc = markRaw(doc)
				this.numPages = this.pdfDoc.numPages
				this.loading = false
				await this.$nextTick()
				await this.$nextTick()
				await this.computeFitPageScale()
				this.scaleFactor = this.fitPageScale
				await this.renderAllPages()
				if (this.$refs.scrollEl) {
					this.$refs.scrollEl.addEventListener('scroll', this.onScroll, { passive: true })
				}
			} catch (e) {
				this.error = e.message || t('ereader', 'Failed to load PDF')
				this.loading = false
			}
		},
		async computeFitPageScale() {
			if (!this.pdfDoc || !this.$refs.scrollEl) return
			const scrollEl = this.$refs.scrollEl
			const w = scrollEl.clientWidth || 800
			const h = scrollEl.clientHeight || 600
			const firstPage = await this.pdfDoc.getPage(1)
			const vp = firstPage.getViewport({ scale: 1 })
			const scaleW = w / vp.width
			const scaleH = h / vp.height
			this.fitPageScale = Math.min(scaleW, scaleH, this.maxScale)
			if (this.scaleFactor === 1) {
				this.scaleFactor = this.fitPageScale
			}
		},
		async renderAllPages() {
			if (!this.pdfDoc || !this.$refs.containerEl || !this.$refs.scrollEl) return
			const scrollEl = this.$refs.scrollEl
			const baseWidth = scrollEl.clientWidth || 800
			for (let n = 1; n <= this.numPages; n++) {
				const canvas = this.canvasRefs[n]
				if (!canvas) continue
				const page = await this.pdfDoc.getPage(n)
				const vp1 = page.getViewport({ scale: 1 })
				const baseScale = baseWidth / vp1.width
				const effectiveScale = baseScale * this.scaleFactor * this.pixelRatio
				const viewport = page.getViewport({ scale: effectiveScale })
				canvas.width = viewport.width
				canvas.height = viewport.height
				canvas.style.width = (viewport.width / this.pixelRatio) + 'px'
				canvas.style.height = (viewport.height / this.pixelRatio) + 'px'
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
	display: flex;
	flex-direction: column;
	background: var(--ereader-surface);
}

.pdf-viewer__toolbar {
	flex-shrink: 0;
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	gap: 0.5rem 1rem;
	padding: 0.5rem 0.75rem;
	background: var(--ereader-action-bar);
	color: var(--ereader-primary-text);
	border-bottom: 1px solid var(--ereader-border);
}

.pdf-viewer__toolbar-group {
	display: flex;
	align-items: center;
	gap: 0.25rem;
}

.pdf-viewer__toolbar-group--page {
	margin-left: auto;
}

.pdf-viewer__btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-width: 36px;
	height: 36px;
	padding: 0 0.5rem;
	border: none;
	border-radius: var(--ereader-radius-sm);
	background: transparent;
	color: inherit;
	cursor: pointer;
	transition: background 0.15s;
}

.pdf-viewer__btn:hover:not(:disabled) {
	background: rgba(255, 255, 255, 0.2);
}

.pdf-viewer__btn:disabled {
	opacity: 0.5;
	cursor: not-allowed;
}

.pdf-viewer__btn--text {
	font-size: 0.875rem;
}

.pdf-viewer__icon {
	width: 24px;
	height: 24px;
}

.pdf-viewer__scale {
	min-width: 3.5em;
	font-size: 0.875rem;
	text-align: center;
}

.pdf-viewer__page-indicator {
	font-size: 0.875rem;
	white-space: nowrap;
}

.pdf-viewer__scroll {
	flex: 1;
	min-height: 0;
	overflow-x: auto;
	overflow-y: auto;
	scroll-behavior: smooth;
	scroll-snap-type: x mandatory;
}

.pdf-viewer__container {
	display: flex;
	flex-direction: row;
	flex-wrap: nowrap;
	align-items: flex-start;
	justify-content: flex-start;
	padding: 1rem;
	gap: 1rem;
	min-height: min-content;
}

.pdf-viewer__page-wrap {
	flex: 0 0 auto;
	scroll-snap-align: center;
	scroll-snap-stop: always;
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
	background: var(--ereader-surface);
}

.pdf-viewer__error {
	color: var(--ereader-error);
}
</style>
