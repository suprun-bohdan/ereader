<?php

declare(strict_types=1);

namespace OCA\Ereader\Service;

use OCA\Ereader\Db\Bookmark;
use OCA\Ereader\Db\BookmarkMapper;
use OCP\IUserSession;

class BookmarkService {

	public function __construct(
		private BookmarkMapper $bookmarkMapper,
		private IUserSession $userSession,
	) {
	}

	/**
	 * @return array<int, array{id: int, fileId: int, position: string, note: string|null, createdAt: string}>
	 */
	public function listByFile(int $fileId): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return [];
		}
		$entities = $this->bookmarkMapper->findByUserAndFile($user->getUID(), $fileId);
		$out = [];
		foreach ($entities as $e) {
			$out[] = [
				'id' => $e->getId(),
				'fileId' => $e->getFileId(),
				'position' => $e->getPosition(),
				'note' => $e->getNote(),
				'createdAt' => $e->getCreatedAt(),
			];
		}
		return $out;
	}

	public function add(int $fileId, string $position, ?string $note = null): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			throw new \RuntimeException('Not authenticated');
		}
		$entity = new Bookmark();
		$entity->setUserId($user->getUID());
		$entity->setFileId($fileId);
		$entity->setPosition($position);
		$entity->setNote($note ?? '');
		$entity->setCreatedAt((new \DateTime())->format('Y-m-d H:i:s'));
		$inserted = $this->bookmarkMapper->insert($entity);
		return [
			'id' => $inserted->getId(),
			'fileId' => $inserted->getFileId(),
			'position' => $inserted->getPosition(),
			'note' => $inserted->getNote(),
			'createdAt' => $inserted->getCreatedAt(),
		];
	}

	public function delete(int $bookmarkId): void {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return;
		}
		try {
			$entity = $this->bookmarkMapper->find($bookmarkId);
			if ($entity !== null && $entity->getUserId() === $user->getUID()) {
				$this->bookmarkMapper->delete($entity);
			}
		} catch (\Exception $e) {
			// ignore
		}
	}
}
