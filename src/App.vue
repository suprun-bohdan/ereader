<template>
	<div id="ereader-app-root" class="ereader-app-root" :data-theme="theme">
		<AppDrawer
			:is-open="drawerOpen"
			:is-mobile="isMobile"
			@close="drawerOpen = false"
		/>
		<div class="ereader-app-root__main">
			<header v-if="showAppBar" class="ereader-app-root__bar">
				<router-link
					v-if="isReadView"
					:to="{ name: 'Library' }"
					class="ereader-app-root__menu-btn"
					aria-label="Back to library"
				>
					<svg class="ereader-app-root__menu-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
					</svg>
				</router-link>
				<button
					v-else
					type="button"
					class="ereader-app-root__menu-btn"
					aria-label="Open menu"
					@click="drawerOpen = true"
				>
					<svg class="ereader-app-root__menu-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
					</svg>
				</button>
				<span class="ereader-app-root__bar-title">{{ appBarTitle }}</span>
			</header>
			<main class="ereader-app-root__content">
				<router-view />
			</main>
		</div>
	</div>
</template>

<script>
import { translate as t } from '@nextcloud/l10n'
import AppDrawer from './components/AppDrawer.vue'

const MOBILE_BREAKPOINT = 768

export default {
	name: 'App',
	components: { AppDrawer },
	data() {
		return {
			drawerOpen: false,
			windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1024,
			theme: 'default',
		}
	},
	computed: {
		isMobile() {
			return this.windowWidth <= MOBILE_BREAKPOINT
		},
		showAppBar() {
			return this.isMobile
		},
		isReadView() {
			return this.$route.name === 'Read'
		},
		appBarTitle() {
			const name = this.$route.name
			if (name === 'Library') return t('ereader', 'Library')
			if (name === 'Setup') return t('ereader', 'Choose books folder')
			if (name === 'Read') return t('ereader', 'Reading')
			return t('ereader', 'e-Reader')
		},
	},
	mounted() {
		window.addEventListener('resize', this.onResize)
		this.onResize()
	},
	beforeUnmount() {
		window.removeEventListener('resize', this.onResize)
	},
	methods: {
		onResize() {
			this.windowWidth = window.innerWidth
			if (!this.isMobile) this.drawerOpen = false
		},
	},
}
</script>

<style scoped>
.ereader-app-root {
	min-height: 100vh;
	width: 100%;
	max-width: 100vw;
	box-sizing: border-box;
	display: flex;
	background: var(--ereader-background);
	color: var(--ereader-text);
}

.ereader-app-root__main {
	flex: 1;
	display: flex;
	flex-direction: column;
	min-height: 100vh;
	margin-left: 0;
	transition: margin-left 0.2s ease;
}

@media (min-width: 769px) {
	.ereader-app-root__main {
		margin-left: var(--ereader-drawer-width, 260px);
	}
}

.ereader-app-root__bar {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	flex-shrink: 0;
	min-height: var(--ereader-app-bar-height);
	padding: 0 1rem;
	background: var(--ereader-action-bar);
	color: var(--ereader-primary-text);
	box-shadow: var(--ereader-card-shadow);
}

.ereader-app-root__menu-btn {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 48px;
	height: 48px;
	padding: 0;
	margin: 0 -0.5rem 0 0;
	border: none;
	background: transparent;
	color: inherit;
	text-decoration: none;
	cursor: pointer;
	border-radius: var(--ereader-radius-sm);
}

.ereader-app-root__menu-btn:hover {
	background: rgba(255, 255, 255, 0.15);
}

.ereader-app-root__menu-icon {
	width: 24px;
	height: 24px;
}

.ereader-app-root__bar-title {
	font-size: 1.25rem;
	font-weight: 500;
}

.ereader-app-root__content {
	flex: 1;
	min-height: 0;
	overflow: auto;
}
</style>
