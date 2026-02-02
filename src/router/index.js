import { createRouter, createWebHashHistory } from 'vue-router'
import { getConfig } from './api'

const routes = [
	{
		path: '/setup',
		name: 'Setup',
		component: () => import('../views/Setup.vue'),
		meta: { requiresSetup: true },
	},
	{
		path: '/',
		name: 'Library',
		component: () => import('../views/Library.vue'),
	},
	{
		path: '/dictionary',
		name: 'Dictionary',
		component: () => import('../views/Dictionary.vue'),
	},
	{
		path: '/read/:fileId',
		name: 'Read',
		component: () => import('../views/Read.vue'),
		props: true,
	},
]

const router = createRouter({
	history: createWebHashHistory(''),
	routes,
})

router.beforeEach(async (to, from, next) => {
	if (to.name === 'Setup' || to.name === 'Dictionary') {
		next()
		return
	}
	try {
		const config = await getConfig()
		if (config.books_folder === undefined || config.books_folder === '') {
			next({ name: 'Setup' })
			return
		}
	} catch (e) {
		next({ name: 'Setup' })
		return
	}
	next()
})

export default router
