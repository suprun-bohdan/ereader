<template>
	<div class="ereader-dictionary">
		<header class="ereader-dictionary__header">
			<h1 class="ereader-dictionary__title">{{ t('ereader', 'Dictionary') }}</h1>
			<p class="ereader-dictionary__desc">{{ t('ereader', 'Words you add from books. Add Ukrainian translation and save.') }}</p>
		</header>

		<div v-if="loading" class="ereader-dictionary__loading">{{ t('ereader', 'Loading…') }}</div>
		<div v-else-if="error" class="ereader-dictionary__error">{{ error }}</div>
		<ul v-else class="ereader-dictionary__list">
			<li v-for="entry in entries" :key="entry.id" class="ereader-dictionary__item">
				<div class="ereader-dictionary__word">{{ entry.word }}</div>
				<div class="ereader-dictionary__row">
					<input
						v-model="entry.translation"
						type="text"
						class="ereader-dictionary__input"
						:placeholder="t('ereader', 'Ukrainian translation')"
						@blur="saveTranslation(entry)"
					/>
					<button
						type="button"
						class="ereader-dictionary__btn-delete"
						:title="t('ereader', 'Remove from dictionary')"
						@click="removeEntry(entry)"
					>
						<svg class="ereader-dictionary__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
							<path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
						</svg>
					</button>
				</div>
			</li>
			<li v-if="entries.length === 0" class="ereader-dictionary__empty">
				{{ t('ereader', 'No words yet. Select text in a book and click "Add to dictionary".') }}
			</li>
		</ul>
	</div>
</template>

<script>
import { getDictionary, updateDictionaryEntry, deleteDictionaryEntry } from '../router/api'
import { translate as t } from '@nextcloud/l10n'

export default {
	name: 'Dictionary',
	data() {
		return {
			entries: [],
			loading: true,
			error: '',
			savingId: null,
		}
	},
	async mounted() {
		await this.load()
	},
	methods: {
		t,
		async load() {
			this.loading = true
			this.error = ''
			try {
				this.entries = await getDictionary()
			} catch (e) {
				this.error = e.message || t('ereader', 'Failed to load dictionary')
			} finally {
				this.loading = false
			}
		},
		async saveTranslation(entry) {
			if (this.savingId === entry.id) return
			this.savingId = entry.id
			try {
				await updateDictionaryEntry(entry.id, entry.translation || null)
			} catch (e) {
				this.error = e.message || t('ereader', 'Failed to save')
			} finally {
				this.savingId = null
			}
		},
		async removeEntry(entry) {
			try {
				await deleteDictionaryEntry(entry.id)
				this.entries = this.entries.filter((e) => e.id !== entry.id)
			} catch (e) {
				this.error = e.message || t('ereader', 'Failed to remove')
			}
		},
	},
}
</script>

<style scoped>
.ereader-dictionary {
	padding: 1.5rem;
	max-width: 640px;
}

.ereader-dictionary__header {
	margin-bottom: 1.5rem;
}

.ereader-dictionary__title {
	margin: 0 0 0.25rem;
	font-size: 1.5rem;
	color: var(--ereader-text);
}

.ereader-dictionary__desc {
	margin: 0;
	font-size: 0.9rem;
	color: var(--ereader-text-secondary);
}

.ereader-dictionary__list {
	list-style: none;
	margin: 0;
	padding: 0;
}

.ereader-dictionary__item {
	margin-bottom: 1rem;
	padding: 0.75rem 1rem;
	background: var(--ereader-surface);
	border-radius: var(--ereader-radius);
	box-shadow: var(--ereader-card-shadow);
	border: 1px solid var(--ereader-border);
}

.ereader-dictionary__word {
	font-weight: 600;
	color: var(--ereader-text);
	margin-bottom: 0.5rem;
	word-break: break-word;
}

.ereader-dictionary__row {
	display: flex;
	gap: 0.5rem;
	align-items: center;
}

.ereader-dictionary__input {
	flex: 1;
	padding: 0.4rem 0.5rem;
	font-size: 1rem;
	border: 1px solid var(--ereader-border);
	border-radius: var(--ereader-radius-sm);
	background: var(--ereader-surface);
	color: var(--ereader-text);
}

.ereader-dictionary__input::placeholder {
	color: var(--ereader-text-secondary);
}

.ereader-dictionary__btn-delete {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 36px;
	height: 36px;
	padding: 0;
	border: none;
	border-radius: var(--ereader-radius-sm);
	background: transparent;
	color: var(--ereader-text-secondary);
	cursor: pointer;
}

.ereader-dictionary__btn-delete:hover {
	background: var(--ereader-background);
	color: var(--ereader-error);
}

.ereader-dictionary__icon {
	width: 20px;
	height: 20px;
}

.ereader-dictionary__loading,
.ereader-dictionary__error,
.ereader-dictionary__empty {
	padding: 2rem;
	text-align: center;
	color: var(--ereader-text-secondary);
}

.ereader-dictionary__error {
	color: var(--ereader-error);
}
</style>
