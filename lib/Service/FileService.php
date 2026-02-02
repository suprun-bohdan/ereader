<?php

declare(strict_types=1);

namespace OCA\Ereader\Service;

use OCP\Files\File;
use OCP\Files\IRootFolder;
use OCP\IUserSession;

class FileService {

	private const MIME_EPUB = 'application/epub+zip';
	private const MIME_PDF = 'application/pdf';
	private const ALLOWED_MIMES = [self::MIME_EPUB, self::MIME_PDF];

	public function __construct(
		private IRootFolder $rootFolder,
		private IUserSession $userSession,
	) {
	}

	public function getFileForUser(int $fileId, string $userId): ?File {
		$userFolder = $this->rootFolder->getUserFolder($userId);
		$nodes = $userFolder->getById($fileId);
		if ($nodes === []) {
			return null;
		}
		$node = $nodes[0];
		if (!$node instanceof File) {
			return null;
		}
		$mime = $node->getMimeType();
		if (!in_array($mime, self::ALLOWED_MIMES, true)) {
			return null;
		}
		return $node;
	}

	public function getFileById(int $fileId): ?File {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return null;
		}
		return $this->getFileForUser($fileId, $user->getUID());
	}
}
