<template>
	<aside
		class="app-drawer"
		:class="{ 'app-drawer--open': isOpen }"
		aria-label="Main navigation"
	>
		<div class="app-drawer__header">
			<span class="app-drawer__title">{{ t('ereader', 'e-Reader') }}</span>
			<button
				v-if="isMobile"
				type="button"
				class="app-drawer__close"
				aria-label="Close menu"
				@click="$emit('close')"
			>
				<svg class="app-drawer__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
					<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
				</svg>
			</button>
		</div>
		<nav class="app-drawer__nav">
			<router-link
				:to="{ name: 'Library' }"
				class="app-drawer__item"
				active-class="app-drawer__item--active"
				@click="onNavigate"
			>
				<svg class="app-drawer__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
					<path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
				</svg>
				<span>{{ t('ereader', 'Library') }}</span>
			</router-link>
			<router-link
				:to="{ name: 'Setup' }"
				class="app-drawer__item"
				active-class="app-drawer__item--active"
				@click="onNavigate"
			>
				<svg class="app-drawer__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
					<path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
				</svg>
				<span>{{ t('ereader', 'Change books folder') }}</span>
			</router-link>
			<a
				:href="filesUrl"
				class="app-drawer__item app-drawer__item--external"
				target="_self"
			>
				<svg class="app-drawer__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
					<path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/>
				</svg>
				<span>{{ t('ereader', 'Back to Files') }}</span>
			</a>
		</nav>
	</aside>
	<div
		v-if="isMobile && isOpen"
		class="app-drawer__backdrop"
		aria-hidden="true"
		@click="$emit('close')"
	/>
</template>

<script>
import { translate as t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'

const FILES_APP_PATH = '/apps/files/'

export default {
	name: 'AppDrawer',
	props: {
		isOpen: { type: Boolean, default: false },
		isMobile: { type: Boolean, default: false },
	},
	emits: ['close'],
	computed: {
		filesUrl() {
			return generateUrl(FILES_APP_PATH)
		},
	},
	methods: {
		t,
		onNavigate() {
			this.$emit('close')
		},
	},
}
</script>

<style scoped>
.app-drawer {
	position: fixed;
	top: var(--nextcloud-header-height, 50px);
	left: 0;
	z-index: 100;
	width: var(--ereader-drawer-width, 260px);
	height: calc(100vh - var(--nextcloud-header-height, 50px));
	background: var(--ereader-surface);
	box-shadow: var(--ereader-card-shadow);
	display: flex;
	flex-direction: column;
	transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.app-drawer__header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 1rem 1.25rem;
	min-height: var(--ereader-app-bar-height);
	border-bottom: 1px solid var(--ereader-border);
}

.app-drawer__title {
	font-size: 1.25rem;
	font-weight: 600;
	color: var(--ereader-text);
}

.app-drawer__close {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 40px;
	height: 40px;
	padding: 0;
	border: none;
	background: transparent;
	color: var(--ereader-text);
	cursor: pointer;
	border-radius: var(--ereader-radius-sm);
}

.app-drawer__close:hover {
	background: var(--ereader-background);
}

.app-drawer__nav {
	flex: 1;
	padding: 0.5rem 0;
	overflow-y: auto;
}

.app-drawer__item {
	display: flex;
	align-items: center;
	gap: 1rem;
	padding: 0.75rem 1.25rem;
	color: var(--ereader-text);
	text-decoration: none;
	font-size: 1rem;
	transition: background 0.15s;
}

.app-drawer__item:hover {
	background: var(--ereader-background);
}

.app-drawer__item--active {
	background: var(--ereader-background);
	color: var(--ereader-primary);
	font-weight: 500;
}

.app-drawer__item--external {
	color: var(--ereader-text-secondary);
}

.app-drawer__icon {
	width: 24px;
	height: 24px;
	flex-shrink: 0;
}

.app-drawer__backdrop {
	position: fixed;
	top: var(--nextcloud-header-height, 50px);
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 99;
	background: rgba(0, 0, 0, 0.4);
}

@media (max-width: 768px) {
	.app-drawer {
		transform: translateX(-100%);
	}
	.app-drawer--open {
		transform: translateX(0);
	}
}

@media (min-width: 769px) {
	.app-drawer__close {
		display: none;
	}
}
</style>
