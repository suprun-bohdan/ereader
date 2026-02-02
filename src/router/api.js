import axios from '@nextcloud/axios'
import { getRequestToken } from '@nextcloud/auth'
import { generateUrl } from '@nextcloud/router'

const api = axios.create({
	baseURL: generateUrl('/apps/ereader/api/v1'),
	headers: {
		requesttoken: getRequestToken(),
	},
})

export async function getConfig() {
	const { data } = await api.get('/config')
	return data
}

export async function setConfig(booksFolder) {
	const { data } = await api.post('/config', new URLSearchParams({ books_folder: booksFolder }), {
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
	})
	return data
}

export async function getFolders(path = '') {
	const { data } = await api.get('/folders', { params: { path } })
	return data
}

export async function getBooks() {
	const { data } = await api.get('/books')
	return data
}

export async function getProgress(fileId) {
	const { data } = await api.get(`/progress/${fileId}`)
	return data
}

export async function saveProgress(fileId, progressData) {
	await api.put(`/progress/${fileId}`, { data: progressData })
}

export async function getBookmarks(fileId) {
	const { data } = await api.get(`/bookmarks/${fileId}`)
	return data
}

export async function createBookmark(fileId, position, note) {
	const { data } = await api.post(`/bookmarks/${fileId}`, { position, note })
	return data
}

export async function deleteBookmark(id) {
	await api.delete(`/bookmarks/${id}`)
}

export function getStreamUrl(fileId) {
	return generateUrl(`/apps/ereader/api/v1/file/${fileId}/stream`)
}
