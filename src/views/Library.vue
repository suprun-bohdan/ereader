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
			<BookCard v-for="book in books" :key="book.id" :book="book" />
			<li v-if="books.length === 0" class="ereader-library__empty">
				{{ t('ereader', 'No books found. Add EPUB or PDF files to your books folder.') }}
			</li>
		</ul>
	</div>
</template>

<script>
import { getBooks } from '../router/api'
import { translate as t } from '@nextcloud/l10n'
import BookCard from '../components/BookCard.vue'

export default {
	name: 'Library',
	components: { BookCard },
	data() {
		return {
			books: [],
			loading: true,
			error: '',
		}
	},
	async mounted() {
		try {
			const data = await getBooks()
			this.books = Array.isArray(data) ? data : (data?.books ?? [])
		} catch (e) {
			this.error = e.message || t('ereader', 'Failed to load books')
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
	min-height: 100%;
	background: var(--ereader-shelf-background);
	border-top: 3px solid var(--ereader-shelf-divider);
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
	color: var(--ereader-text);
}
.ereader-library__setup-link {
	font-size: 0.9rem;
	color: var(--ereader-primary);
	text-decoration: none;
	font-weight: 500;
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
	gap: 1.25rem;
}
.ereader-library__loading,
.ereader-library__error,
.ereader-library__empty {
	padding: 2rem;
	text-align: center;
	color: var(--ereader-text-secondary);
}
.ereader-library__error {
	color: var(--ereader-error);
}
</style>
