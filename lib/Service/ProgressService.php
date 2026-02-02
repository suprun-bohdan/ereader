<?php

declare(strict_types=1);

namespace OCA\Ereader\Service;

use OCA\Ereader\Db\Progress;
use OCA\Ereader\Db\ProgressMapper;
use OCP\IUserSession;

class ProgressService {

	public function __construct(
		private ProgressMapper $progressMapper,
		private IUserSession $userSession,
	) {
	}

	public function getProgress(int $fileId): ?array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return null;
		}
		$entity = $this->progressMapper->findByUserAndFile($user->getUID(), $fileId);
		if ($entity === null) {
			return null;
		}
		$data = json_decode($entity->getProgressData(), true);
		return is_array($data) ? $data : null;
	}

	public function saveProgress(int $fileId, array $data): void {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return;
		}
		$entity = $this->progressMapper->findByUserAndFile($user->getUID(), $fileId);
		$now = (new \DateTime())->format('Y-m-d H:i:s');
		if ($entity !== null) {
			$entity->setProgressData(json_encode($data));
			$entity->setUpdatedAt($now);
			$this->progressMapper->update($entity);
		} else {
			$entity = new Progress();
			$entity->setUserId($user->getUID());
			$entity->setFileId($fileId);
			$entity->setProgressData(json_encode($data));
			$entity->setUpdatedAt($now);
			$this->progressMapper->insert($entity);
		}
	}

	/**
	 * @return array<int, array{fileId: int, progress: array, updatedAt: string}>
	 */
	public function getRecent(int $limit = 10): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return [];
		}
		$entities = $this->progressMapper->findRecentByUser($user->getUID(), $limit);
		$out = [];
		foreach ($entities as $e) {
			$data = json_decode($e->getProgressData(), true);
			$out[] = [
				'fileId' => $e->getFileId(),
				'progress' => is_array($data) ? $data : [],
				'updatedAt' => $e->getUpdatedAt(),
			];
		}
		return $out;
	}
}
