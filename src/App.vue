<template>
	<div id="ereader-app-root" class="ereader-app-root">
		<router-view />
	</div>
</template>

<script>
export default {
	name: 'App',
}
</script>

<style scoped>
.ereader-app-root {
	min-height: 100vh;
}
</style>
