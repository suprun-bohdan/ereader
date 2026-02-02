<template>
	<div class="epub-viewer" @mouseup="onMouseUp">
		<div ref="viewerEl" class="epub-viewer__area"></div>
		<SelectionToolbar
			:visible="toolbarVisible"
			:position="toolbarPosition"
			@add="onAddToDictionary"
		/>
		<div v-if="loading" class="epub-viewer__loading">{{ t('ereader', 'Loading…') }}</div>
		<div v-else-if="error" class="epub-viewer__error">{{ error }}</div>
	</div>
</template>

<script>
import { translate as t } from '@nextcloud/l10n'
import SelectionToolbar from './SelectionToolbar.vue'

export default {
	name: 'EpubViewer',
	components: { SelectionToolbar },
	props: {
		url: { type: String, required: true },
	},
	emits: ['add-to-dictionary'],
	data() {
		return {
			loading: true,
			error: '',
			rendition: null,
			book: null,
			toolbarVisible: false,
			toolbarPosition: { top: 0, left: 0 },
			selectedText: '',
		}
	},
	async mounted() {
		try {
			const { default: ePub } = await import('epubjs')
			this.book = ePub(this.url)
			await this.book.ready
			this.rendition = this.book.renderTo(this.$refs.viewerEl, {
				width: '100%',
				height: '100%',
				spread: 'none',
			})
			await this.rendition.display()
			this.loading = false
			this.$nextTick(() => this.attachSelectionListener())
		} catch (e) {
			this.error = e.message || this.t('ereader', 'Failed to load book')
			this.loading = false
		}
	},
	beforeUnmount() {
		this.detachSelectionListener()
		if (this.rendition) {
			this.rendition.destroy()
		}
		if (this.book) {
			this.book.destroy()
		}
	},
	methods: {
		t,
		attachSelectionListener() {
			const iframe = this.$refs.viewerEl?.querySelector('iframe')
			const doc = iframe ? iframe.contentDocument : document
			const win = iframe ? iframe.contentWindow : window
			if (!doc || !win) return
			this._selectionDoc = doc
			this._selectionWin = win
			doc.addEventListener('mouseup', this.onSelectionMouseUp)
		},
		detachSelectionListener() {
			if (this._selectionDoc) {
				this._selectionDoc.removeEventListener('mouseup', this.onSelectionMouseUp)
				this._selectionDoc = null
			}
			this._selectionWin = null
		},
		onMouseUp() {
			this.updateToolbarFromSelection(document, window)
		},
		onSelectionMouseUp() {
			if (this._selectionWin) {
				this.updateToolbarFromSelection(this._selectionDoc, this._selectionWin)
			}
		},
		updateToolbarFromSelection(doc, win) {
			const sel = win.getSelection()
			const text = (sel && sel.toString ? sel.toString() : '').trim()
			this.selectedText = text
			if (!text) {
				this.toolbarVisible = false
				return
			}
			let top = 0
			let left = 0
			try {
				const range = sel.getRangeAt(0)
				const rect = range.getBoundingClientRect()
				const iframe = this.$refs.viewerEl?.querySelector('iframe')
				if (iframe && doc !== document) {
					const iframeRect = iframe.getBoundingClientRect()
					top = iframeRect.top + rect.top + rect.height / 2
					left = iframeRect.left + rect.left + rect.width / 2
				} else {
					top = rect.top + rect.height / 2
					left = rect.left + rect.width / 2
				}
			} catch (_) {}
			this.toolbarPosition = { top, left }
			this.toolbarVisible = true
		},
		onAddToDictionary() {
			if (this.selectedText) {
				this.$emit('add-to-dictionary', this.selectedText)
				this.toolbarVisible = false
				this.selectedText = ''
				if (this._selectionWin && this._selectionWin.getSelection()) {
					this._selectionWin.getSelection().removeAllRanges()
				}
			}
		},
	},
}
</script>

<style scoped>
.epub-viewer {
	position: relative;
	width: 100%;
	height: 100%;
}
.epub-viewer__area {
	width: 100%;
	height: 100%;
	background: var(--color-main-background, #fff);
}
.epub-viewer__loading,
.epub-viewer__error {
	position: absolute;
	inset: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 2rem;
	text-align: center;
}
.epub-viewer__error {
	color: var(--color-error, #c00);
}
</style>
