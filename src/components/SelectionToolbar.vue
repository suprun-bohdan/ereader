<template>
	<Teleport to="body">
		<div
			v-if="visible"
			class="selection-toolbar"
			:style="toolbarStyle"
			role="toolbar"
		>
			<button
				type="button"
				class="selection-toolbar__btn"
				@click="onAdd"
			>
				{{ t('ereader', 'Add to dictionary') }}
			</button>
		</div>
	</Teleport>
</template>

<script>
import { translate as t } from '@nextcloud/l10n'

export default {
	name: 'SelectionToolbar',
	props: {
		visible: { type: Boolean, default: false },
		position: {
			type: Object,
			default: () => ({ top: 0, left: 0 }),
		},
	},
	emits: ['add'],
	computed: {
		toolbarStyle() {
			const { top, left } = this.position
			return {
				top: `${top}px`,
				left: `${left}px`,
			}
		},
	},
	methods: {
		t,
		onAdd() {
			this.$emit('add')
		},
	},
}
</script>

<style scoped>
.selection-toolbar {
	position: fixed;
	z-index: 1000;
	transform: translate(-50%, -100%);
	margin-top: -8px;
	padding: 4px 8px;
	background: var(--ereader-action-bar);
	color: var(--ereader-primary-text);
	border-radius: var(--ereader-radius-sm);
	box-shadow: var(--ereader-card-shadow);
}

.selection-toolbar__btn {
	padding: 0.35rem 0.75rem;
	font-size: 0.875rem;
	border: none;
	border-radius: var(--ereader-radius-sm);
	background: rgba(255, 255, 255, 0.2);
	color: inherit;
	cursor: pointer;
}

.selection-toolbar__btn:hover {
	background: rgba(255, 255, 255, 0.3);
}
</style>
