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
	width: 100%;
	max-width: 100vw;
	box-sizing: border-box;
}
</style>
