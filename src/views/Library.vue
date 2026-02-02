<template>
	<div class="ereader-library">
		<header class="ereader-library__header">
			<h1 class="ereader-library__title">{{ t('ereader', 'Library') }}</h1>
			<router-link :to="{ name: 'Setup' }" class="ereader-library__setup-link">
				{{ t('ereader', 'Change books folder') }}
			</router-link>
		</header>

		<div v-if="loading" class="ereader-library__loading">{{ t('ereader', 'Loading…') }}</div>
		<div v-else-if="error" class="ereader-library__error">{{ error }}</div>
		<ul v-else class="ereader-library__list">
			<li v-for="book in books" :key="book.id" class="ereader-library__item">
				<router-link :to="{ name: 'Read', params: { fileId: book.id }, query: { mime: book.mime } }" class="ereader-library__link">
					<div class="ereader-library__cover">
						<span class="ereader-library__cover-icon">{{ book.mime === 'application/pdf' ? '📕' : '📘' }}</span>
					</div>
					<span class="ereader-library__name">{{ book.name }}</span>
				</router-link>
			</li>
			<li v-if="books.length === 0" class="ereader-library__empty">
				{{ t('ereader', 'No books found. Add EPUB or PDF files to your books folder.') }}
			</li>
		</ul>
	</div>
</template>

<script>
import { getBooks } from '../router/api'
import { translate as t } from '@nextcloud/l10n'

export default {
	name: 'Library',
	data() {
		return {
			books: [],
			loading: true,
			error: '',
		}
	},
	async mounted() {
		try {
			this.books = await getBooks()
		} catch (e) {
			this.error = e.message || this.t('ereader', 'Failed to load books')
		} finally {
			this.loading = false
		}
	},
	methods: {
		t,
	},
}
</script>

<style scoped>
.ereader-library {
	padding: 1.5rem;
}
.ereader-library__header {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: space-between;
	gap: 1rem;
	margin-bottom: 1.5rem;
}
.ereader-library__title {
	margin: 0;
	font-size: 1.5rem;
}
.ereader-library__setup-link {
	font-size: 0.9rem;
	color: var(--color-primary-element, #0082c9);
	text-decoration: none;
}
.ereader-library__setup-link:hover {
	text-decoration: underline;
}
.ereader-library__list {
	list-style: none;
	margin: 0;
	padding: 0;
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
	gap: 1rem;
}
.ereader-library__item {
	margin: 0;
}
.ereader-library__link {
	display: flex;
	flex-direction: column;
	align-items: center;
	text-decoration: none;
	color: var(--color-main-text, #000);
	padding: 0.75rem;
	border-radius: 8px;
	background: var(--color-background-hover, #f5f5f5);
	transition: background 0.2s;
}
.ereader-library__link:hover {
	background: var(--color-background-dark, #e5e5e5);
}
.ereader-library__cover {
	width: 80px;
	height: 110px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: var(--color-background-dark, #e0e0e0);
	border-radius: 4px;
	margin-bottom: 0.5rem;
}
.ereader-library__cover-icon {
	font-size: 2.5rem;
}
.ereader-library__name {
	font-size: 0.9rem;
	text-align: center;
	word-break: break-word;
}
.ereader-library__loading,
.ereader-library__error,
.ereader-library__empty {
	padding: 2rem;
	text-align: center;
	color: var(--color-text-lighter, #666);
}
.ereader-library__error {
	color: var(--color-error, #c00);
}
</style>
