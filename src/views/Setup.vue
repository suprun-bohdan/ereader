<template>
	<div class="ereader-setup">
		<div class="ereader-setup__card">
			<h1 class="ereader-setup__title">{{ t('ereader', 'Choose your books folder') }}</h1>
			<p class="ereader-setup__desc">{{ t('ereader', 'Select the folder where your EPUB and PDF books are stored. You can also scan all your files.') }}</p>

			<div v-if="loading" class="ereader-setup__loading">{{ t('ereader', 'Loading…') }}</div>

			<template v-else>
				<div class="ereader-setup__breadcrumb">
					<button type="button" class="ereader-setup__breadcrumb-item" @click="path = ''">
						{{ t('ereader', 'Home') }}
					</button>
					<template v-for="(part, i) in pathParts" :key="i">
						<span class="ereader-setup__breadcrumb-sep">/</span>
						<button type="button" class="ereader-setup__breadcrumb-item" @click="path = pathParts.slice(0, i + 1).join('/')">
							{{ part }}
						</button>
					</template>
				</div>

				<div class="ereader-setup__actions">
					<button type="button" class="ereader-setup__btn primary" :disabled="saving" @click="selectCurrent">
						{{ path === '' ? t('ereader', 'Scan all my files') : t('ereader', 'Use this folder') }}
					</button>
				</div>

				<ul class="ereader-setup__folders">
					<li v-for="folder in folders" :key="folder.path" class="ereader-setup__folder">
						<button type="button" class="ereader-setup__folder-btn" @click="path = folder.path">
							<span class="ereader-setup__folder-icon">📁</span>
							{{ folder.name }}
						</button>
					</li>
					<li v-if="folders.length === 0 && !loading" class="ereader-setup__empty">
						{{ t('ereader', 'No subfolders here. Use the button above to select this folder.') }}
					</li>
				</ul>
			</template>

			<p v-if="error" class="ereader-setup__error">{{ error }}</p>
		</div>
	</div>
</template>

<script>
import { getFolders, setConfig } from '../router/api'
import { translate as t } from '@nextcloud/l10n'

export default {
	name: 'Setup',
	data() {
		return {
			path: '',
			folders: [],
			loading: true,
			saving: false,
			error: '',
		}
	},
	computed: {
		pathParts() {
			return this.path ? this.path.split('/').filter(Boolean) : []
		},
	},
	watch: {
		path: {
			immediate: true,
			handler() {
				this.loadFolders()
			},
		},
	},
	methods: {
		t,
		async loadFolders() {
			this.loading = true
			this.error = ''
			try {
				this.folders = await getFolders(this.path)
			} catch (e) {
				this.error = e.message || this.t('ereader', 'Failed to load folders')
				this.folders = []
			} finally {
				this.loading = false
			}
		},
		async selectCurrent() {
			this.saving = true
			this.error = ''
			try {
				await setConfig(this.path)
				this.$router.replace({ name: 'Library' })
			} catch (e) {
				this.error = e.message || this.t('ereader', 'Failed to save')
			} finally {
				this.saving = false
			}
		},
	},
}
</script>

<style scoped>
.ereader-setup {
	padding: 2rem;
	display: flex;
	justify-content: center;
	align-items: flex-start;
	min-height: 80vh;
}
.ereader-setup__card {
	background: var(--color-main-background, #fff);
	border-radius: var(--border-radius-large, 8px);
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	padding: 2rem;
	max-width: 480px;
	width: 100%;
}
.ereader-setup__title {
	margin: 0 0 0.5rem;
	font-size: 1.5rem;
}
.ereader-setup__desc {
	color: var(--color-text-lighter, #666);
	margin: 0 0 1.5rem;
}
.ereader-setup__breadcrumb {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	gap: 0.25rem;
	margin-bottom: 1rem;
	font-size: 0.9rem;
}
.ereader-setup__breadcrumb-item {
	background: none;
	border: none;
	color: var(--color-primary, #0082c9);
	cursor: pointer;
	padding: 0.2rem 0.4rem;
	border-radius: 4px;
}
.ereader-setup__breadcrumb-item:hover {
	text-decoration: underline;
}
.ereader-setup__breadcrumb-sep {
	color: var(--color-text-lighter, #999);
}
.ereader-setup__actions {
	margin-bottom: 1rem;
}
.ereader-setup__btn {
	padding: 0.5rem 1rem;
	border-radius: var(--border-radius, 4px);
	border: none;
	cursor: pointer;
	font-size: 1rem;
}
.ereader-setup__btn.primary {
	background: var(--color-primary, #0082c9);
	color: #fff;
}
.ereader-setup__btn:disabled {
	opacity: 0.6;
	cursor: not-allowed;
}
.ereader-setup__folders {
	list-style: none;
	margin: 0;
	padding: 0;
}
.ereader-setup__folder {
	margin: 0.25rem 0;
}
.ereader-setup__folder-btn {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	width: 100%;
	text-align: left;
	padding: 0.5rem 0.75rem;
	background: var(--color-background-hover, #f5f5f5);
	border: none;
	border-radius: 4px;
	cursor: pointer;
	font-size: 1rem;
}
.ereader-setup__folder-btn:hover {
	background: var(--color-background-dark, #e5e5e5);
}
.ereader-setup__folder-icon {
	font-size: 1.2rem;
}
.ereader-setup__empty {
	color: var(--color-text-lighter, #666);
	padding: 0.5rem 0;
	font-size: 0.9rem;
}
.ereader-setup__loading,
.ereader-setup__error {
	margin: 1rem 0 0;
}
.ereader-setup__error {
	color: var(--color-error, #c00);
}
</style>
