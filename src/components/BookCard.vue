<template>
	<li class="book-card">
		<router-link
			:to="{ name: 'Read', params: { fileId: book.id }, query: { mime: book.mime } }"
			class="book-card__link"
		>
			<div class="book-card__cover">
				<span class="book-card__cover-icon" :aria-hidden="true">{{ coverIcon }}</span>
			</div>
			<span class="book-card__name">{{ book.name }}</span>
		</router-link>
	</li>
</template>

<script>
const MIME_PDF = 'application/pdf'
const MIME_EPUB = 'application/epub+zip'

export default {
	name: 'BookCard',
	props: {
		book: {
			type: Object,
			required: true,
			validator(b) {
				return typeof b.id !== 'undefined' && typeof b.name === 'string'
			},
		},
	},
	computed: {
		coverIcon() {
			const m = this.book.mime
			if (m === MIME_PDF) return '📕'
			if (m === MIME_EPUB) return '📘'
			return '📗'
		},
	},
}
</script>

<style scoped>
.book-card {
	margin: 0;
	list-style: none;
}

.book-card__link {
	display: flex;
	flex-direction: column;
	align-items: center;
	text-decoration: none;
	color: var(--ereader-text);
	padding: 0.75rem;
	border-radius: var(--ereader-radius);
	background: var(--ereader-surface);
	box-shadow: var(--ereader-card-shadow);
	transition: transform 0.15s, box-shadow 0.15s;
}

.book-card__link:hover {
	transform: translateY(-2px);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.book-card__cover {
	width: 80px;
	height: 110px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: var(--ereader-background);
	border-radius: var(--ereader-radius-sm);
	margin-bottom: 0.5rem;
	border: 1px solid var(--ereader-border);
}

.book-card__cover-icon {
	font-size: 2.5rem;
}

.book-card__name {
	font-size: 0.9rem;
	text-align: center;
	word-break: break-word;
	line-height: 1.3;
}
</style>
