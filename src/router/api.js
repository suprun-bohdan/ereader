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
	const res = await api.get('/folders', { params: { path } })
	const data = res.data
	// Unwrap if response is wrapped (e.g. { data: { folders } } or { ocs: { data } })
	const body = data?.data ?? data?.ocs?.data ?? data
	return typeof body === 'object' && body !== null ? body : (data ?? {})
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

/** Fetch file stream as Blob (with CSRF token). Use blob URL in iframe to avoid frame-ancestors blocking. */
export async function getStreamBlob(fileId) {
	const { data } = await api.get(`/file/${fileId}/stream`, { responseType: 'blob' })
	return data
}
