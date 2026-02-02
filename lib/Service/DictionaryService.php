<?php

declare(strict_types=1);

namespace OCA\Ereader\Service;

use OCA\Ereader\Db\Dictionary;
use OCA\Ereader\Db\DictionaryMapper;
use OCP\IUserSession;

class DictionaryService {

	public function __construct(
		private DictionaryMapper $dictionaryMapper,
		private IUserSession $userSession,
	) {
	}

	/**
	 * @return array<int, array{id: int, word: string, translation: string|null, createdAt: string, updatedAt: string}>
	 */
	public function list(): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return [];
		}
		$entities = $this->dictionaryMapper->findByUserId($user->getUID());
		$out = [];
		foreach ($entities as $e) {
			$out[] = [
				'id' => $e->getId(),
				'word' => $e->getWord(),
				'translation' => $e->getTranslation(),
				'createdAt' => $e->getCreatedAt(),
				'updatedAt' => $e->getUpdatedAt(),
			];
		}
		return $out;
	}

	/**
	 * Add word (or update translation if word already exists for user).
	 * @return array{id: int, word: string, translation: string|null, createdAt: string, updatedAt: string}
	 */
	public function add(string $word, ?string $translation = null): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			throw new \RuntimeException('Not authenticated');
		}
		$word = trim($word);
		if ($word === '') {
			throw new \InvalidArgumentException('Word cannot be empty');
		}
		$existing = $this->dictionaryMapper->findByUserAndWord($user->getUID(), $word);
		$now = (new \DateTime())->format('Y-m-d H:i:s');
		if ($existing !== null) {
			$existing->setTranslation($translation ?? $existing->getTranslation());
			$existing->setUpdatedAt($now);
			$this->dictionaryMapper->update($existing);
			return [
				'id' => $existing->getId(),
				'word' => $existing->getWord(),
				'translation' => $existing->getTranslation(),
				'createdAt' => $existing->getCreatedAt(),
				'updatedAt' => $existing->getUpdatedAt(),
			];
		}
		$entity = new Dictionary();
		$entity->setUserId($user->getUID());
		$entity->setWord($word);
		$entity->setTranslation($translation);
		$entity->setCreatedAt($now);
		$entity->setUpdatedAt($now);
		$inserted = $this->dictionaryMapper->insert($entity);
		return [
			'id' => $inserted->getId(),
			'word' => $inserted->getWord(),
			'translation' => $inserted->getTranslation(),
			'createdAt' => $inserted->getCreatedAt(),
			'updatedAt' => $inserted->getUpdatedAt(),
		];
	}

	public function updateTranslation(int $id, ?string $translation): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			throw new \RuntimeException('Not authenticated');
		}
		$entity = $this->dictionaryMapper->find($id);
		if ($entity === null || $entity->getUserId() !== $user->getUID()) {
			throw new \RuntimeException('Entry not found');
		}
		$entity->setTranslation($translation);
		$entity->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));
		$this->dictionaryMapper->update($entity);
		return [
			'id' => $entity->getId(),
			'word' => $entity->getWord(),
			'translation' => $entity->getTranslation(),
			'createdAt' => $entity->getCreatedAt(),
			'updatedAt' => $entity->getUpdatedAt(),
		];
	}

	public function delete(int $id): void {
		$user = $this->userSession->getUser();
		if ($user === null) {
			throw new \RuntimeException('Not authenticated');
		}
		$entity = $this->dictionaryMapper->find($id);
		if ($entity !== null && $entity->getUserId() === $user->getUID()) {
			$this->dictionaryMapper->delete($entity);
		}
	}
}
