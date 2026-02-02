<?php

declare(strict_types=1);

namespace OCA\Ereader\Db;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class DictionaryMapper extends QBMapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'ereader_dictionary', Dictionary::class);
	}

	/** @return Dictionary[] */
	public function findByUserId(string $userId): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->orderBy('word', 'ASC');
		$result = $qb->executeQuery();
		$rows = $result->fetchAllAssociative();
		$result->closeCursor();
		return array_map([$this, 'mapRowToEntity'], $rows);
	}

	public function findByUserAndWord(string $userId, string $word): ?Dictionary {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->andWhere($qb->expr()->eq('word', $qb->createNamedParameter($word)));
		$result = $qb->executeQuery();
		$row = $result->fetchAssociative();
		$result->closeCursor();
		return $row ? $this->mapRowToEntity($row) : null;
	}
}
