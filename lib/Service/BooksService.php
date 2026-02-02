<?php

declare(strict_types=1);

namespace OCA\Ereader\Service;

use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\IUserSession;

class BooksService {

	private const MIME_EPUB = 'application/epub+zip';
	private const MIME_PDF = 'application/pdf';
	private const ALLOWED_MIMES = [self::MIME_EPUB, self::MIME_PDF];

	public function __construct(
		private IRootFolder $rootFolder,
		private IUserSession $userSession,
		private ConfigService $configService,
	) {
	}

	/**
	 * List book files (EPUB/PDF) for the current user, optionally scoped by books_folder.
	 * @return array<int, array{id: int, name: string, path: string, mime: string, size: int, mtime: int}>
	 */
	public function listBooks(): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return [];
		}
		$userId = $user->getUID();
		$userFolder = $this->rootFolder->getUserFolder($userId);
		$booksFolderPath = $this->configService->getBooksFolder($userId);

		$baseFolder = $booksFolderPath === ''
			? $userFolder
			: $this->getFolderAtPath($userFolder, $booksFolderPath);

		if ($baseFolder === null) {
			return [];
		}

		$books = [];
		$this->collectBooksRecursive($baseFolder, $booksFolderPath === '' ? '' : $booksFolderPath, $books);
		return array_values($books);
	}

	private function getFolderAtPath(Folder $userFolder, string $path): ?Folder {
		$path = trim($path, '/');
		if ($path === '') {
			return $userFolder;
		}
		try {
			$node = $userFolder->get($path);
			return $node instanceof Folder ? $node : null;
		} catch (\OCP\Files\NotFoundException $e) {
			return null;
		}
	}

	/**
	 * @param array<int, array{id: int, name: string, path: string, mime: string, size: int, mtime: int}> $out
	 */
	private function collectBooksRecursive(Folder $folder, string $prefix, array &$out): void {
		$listing = $folder->getDirectoryListing();
		foreach ($listing as $node) {
			if ($node instanceof File) {
				$mime = $node->getMimeType();
				if (in_array($mime, self::ALLOWED_MIMES, true)) {
					$path = $prefix === '' ? $node->getName() : $prefix . '/' . $node->getName();
					$out[$node->getId()] = [
						'id' => $node->getId(),
						'name' => $node->getName(),
						'path' => $path,
						'mime' => $mime,
						'size' => $node->getSize(),
						'mtime' => $node->getMTime(),
					];
				}
			} elseif ($node instanceof Folder) {
				$nextPrefix = $prefix === '' ? $node->getName() : $prefix . '/' . $node->getName();
				$this->collectBooksRecursive($node, $nextPrefix, $out);
			}
		}
	}
}
