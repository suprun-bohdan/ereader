<template>
	<div class="epub-viewer">
		<div ref="viewerEl" class="epub-viewer__area"></div>
		<div v-if="loading" class="epub-viewer__loading">{{ t('ereader', 'Loading…') }}</div>
		<div v-else-if="error" class="epub-viewer__error">{{ error }}</div>
	</div>
</template>

<script>
import { translate as t } from '@nextcloud/l10n'

export default {
	name: 'EpubViewer',
	props: {
		url: { type: String, required: true },
	},
	data() {
		return {
			loading: true,
			error: '',
			rendition: null,
			book: null,
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
		} catch (e) {
			this.error = e.message || this.t('ereader', 'Failed to load book')
			this.loading = false
		}
	},
	beforeUnmount() {
		if (this.rendition) {
			this.rendition.destroy()
		}
		if (this.book) {
			this.book.destroy()
		}
	},
	methods: {
		t,
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
