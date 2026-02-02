<template>
	<div class="ereader-setup">
		<div class="ereader-setup__card">
			<h1 class="ereader-setup__title">{{ t('ereader', 'Choose your books folder') }}</h1>
			<p class="ereader-setup__desc">{{ t('ereader', 'Select the folder where your EPUB and PDF books are stored. You can also scan all your files.') }}</p>

			<div v-if="loading" class="ereader-setup__loading">{{ t('ereader', 'Loading…') }}</div>

			<template v-else>
				<p v-if="error" class="ereader-setup__error ereader-setup__error--top">{{ error }}</p>

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
					<li class="ereader-setup__folder ereader-setup__folder--root">
						<button type="button" class="ereader-setup__folder-btn ereader-setup__folder-btn--root" @click="selectCurrent">
							<span class="ereader-setup__folder-icon">📁</span>
							{{ path === '' ? t('ereader', 'My files — use as books folder') : t('ereader', 'Use this folder as books location') }}
						</button>
					</li>
					<li v-for="folder in folders" :key="folder.path || folder.name" class="ereader-setup__folder">
						<button type="button" class="ereader-setup__folder-btn" @click="path = folder.path || ''">
							<span class="ereader-setup__folder-icon">📁</span>
							{{ folder.name }}
						</button>
					</li>
					<li v-if="folders.length === 0 && !loading" class="ereader-setup__empty">
						{{ t('ereader', 'No subfolders in this folder. Use the option above to select this location.') }}
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
				const data = await getFolders(this.path)
				if (data && typeof data === 'object') {
					this.folders = Array.isArray(data.folders) ? data.folders : (Array.isArray(data) ? data : [])
					this.error = data.error ? String(data.error) : ''
				} else {
					this.folders = Array.isArray(data) ? data : []
					this.error = data && data.error ? String(data.error) : ''
				}
			} catch (e) {
				const res = e.response
				const errBody = res?.data
				const errMsg = (errBody && typeof errBody === 'object' && (errBody.error || errBody.message))
					? (errBody.error || errBody.message)
					: (res?.data ? String(res.data) : e.message)
				this.error = errMsg || this.t('ereader', 'Failed to load folders')
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
	background: var(--ereader-surface);
	border-radius: var(--ereader-radius);
	box-shadow: var(--ereader-card-shadow);
	padding: 2rem;
	max-width: 480px;
	width: 100%;
	border: 1px solid var(--ereader-border);
}
.ereader-setup__title {
	margin: 0 0 0.5rem;
	font-size: 1.5rem;
	color: var(--ereader-text);
}
.ereader-setup__desc {
	color: var(--ereader-text-secondary);
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
	color: var(--ereader-primary);
	cursor: pointer;
	padding: 0.35rem 0.5rem;
	border-radius: var(--ereader-radius-sm);
}
.ereader-setup__breadcrumb-item:hover {
	text-decoration: underline;
	background: var(--ereader-background);
}
.ereader-setup__breadcrumb-sep {
	color: var(--ereader-text-secondary);
}
.ereader-setup__actions {
	margin-bottom: 1rem;
}
.ereader-setup__btn {
	padding: 0.6rem 1.25rem;
	border-radius: var(--ereader-radius-sm);
	border: none;
	cursor: pointer;
	font-size: 1rem;
	font-weight: 500;
	transition: background 0.15s;
}
.ereader-setup__btn.primary {
	background: var(--ereader-fill-button);
	color: var(--ereader-primary-text);
}
.ereader-setup__btn.primary:hover:not(:disabled) {
	filter: brightness(1.05);
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
	padding: 0.6rem 0.75rem;
	background: var(--ereader-background);
	border: 1px solid var(--ereader-border);
	border-radius: var(--ereader-radius-sm);
	cursor: pointer;
	font-size: 1rem;
	color: var(--ereader-text);
	transition: background 0.15s;
}
.ereader-setup__folder-btn:hover {
	background: var(--ereader-surface);
	box-shadow: var(--ereader-card-shadow);
}
.ereader-setup__folder--root {
	margin-bottom: 0.5rem;
}
.ereader-setup__folder-btn--root {
	background: var(--ereader-primary);
	color: var(--ereader-primary-text);
	border-color: var(--ereader-primary);
	font-weight: 600;
}
.ereader-setup__folder-btn--root:hover {
	filter: brightness(1.08);
}
.ereader-setup__folder-icon {
	font-size: 1.2rem;
}
.ereader-setup__empty {
	color: var(--ereader-text-secondary);
	padding: 0.5rem 0;
	font-size: 0.9rem;
}
.ereader-setup__loading,
.ereader-setup__error {
	margin: 1rem 0 0;
}
.ereader-setup__error {
	color: var(--ereader-error);
}
</style>
