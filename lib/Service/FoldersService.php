<?php

declare(strict_types=1);

namespace OCA\Ereader\Service;

use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\IUserSession;

class FoldersService {

	public function __construct(
		private IRootFolder $rootFolder,
		private IUserSession $userSession,
	) {
	}

	/**
	 * List direct child folders under the given path (relative to user root).
	 * @return array<int, array{name: string, path: string}>
	 */
	public function listFolders(string $path = ''): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return [];
		}
		$userFolder = $this->rootFolder->getUserFolder($user->getUID());
		$path = trim($path, '/');
		$folder = $path === '' ? $userFolder : $this->getFolderAtPath($userFolder, $path);
		if ($folder === null) {
			return [];
		}
		$out = [];
		$listing = $folder->getDirectoryListing();
		foreach ($listing as $node) {
			if ($node instanceof Folder) {
				$name = $node->getName();
				$subPath = $path === '' ? $name : $path . '/' . $name;
				$out[] = ['name' => $name, 'path' => $subPath];
			}
		}
		usort($out, static fn (array $a, array $b) => strcasecmp($a['name'], $b['name']));
		return $out;
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
}
