<?php

declare(strict_types=1);

return [
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

		['name' => 'config_api#index', 'url' => '/api/v1/config', 'verb' => 'GET'],
		['name' => 'config_api#update', 'url' => '/api/v1/config', 'verb' => 'POST'],
		['name' => 'books_api#index', 'url' => '/api/v1/books', 'verb' => 'GET'],
		['name' => 'folders_api#index', 'url' => '/api/v1/folders', 'verb' => 'GET'],
		['name' => 'file_api#stream', 'url' => '/api/v1/file/{fileId}/stream', 'verb' => 'GET'],
		['name' => 'progress_api#get', 'url' => '/api/v1/progress/{fileId}', 'verb' => 'GET'],
		['name' => 'progress_api#save', 'url' => '/api/v1/progress/{fileId}', 'verb' => 'PUT'],
		['name' => 'progress_api#recent', 'url' => '/api/v1/progress/recent', 'verb' => 'GET'],
		['name' => 'bookmark_api#index', 'url' => '/api/v1/bookmarks/{fileId}', 'verb' => 'GET'],
		['name' => 'bookmark_api#create', 'url' => '/api/v1/bookmarks/{fileId}', 'verb' => 'POST'],
		['name' => 'bookmark_api#delete', 'url' => '/api/v1/bookmarks/{id}', 'verb' => 'DELETE'],

		['name' => 'dictionary_api#index', 'url' => '/api/v1/dictionary', 'verb' => 'GET'],
		['name' => 'dictionary_api#create', 'url' => '/api/v1/dictionary', 'verb' => 'POST'],
		['name' => 'dictionary_api#update', 'url' => '/api/v1/dictionary/{id}', 'verb' => 'PUT'],
		['name' => 'dictionary_api#delete', 'url' => '/api/v1/dictionary/{id}', 'verb' => 'DELETE'],

		['name' => 'config_api#preflighted_cors', 'url' => '/api/v1/{path}', 'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
	],
];
