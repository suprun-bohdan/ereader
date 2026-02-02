<?php

declare(strict_types=1);

namespace OCA\Ereader\Db;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class ProgressMapper extends QBMapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'ereader_progress', Progress::class);
	}

	public function findByUserAndFile(string $userId, int $fileId): ?Progress {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->andWhere($qb->expr()->eq('file_id', $qb->createNamedParameter($fileId, IQueryBuilder::PARAM_INT)));
		$result = $qb->executeQuery();
		$row = $result->fetchAssociative();
		$result->closeCursor();
		return $row ? $this->mapRowToEntity($row) : null;
	}

	/** @return Progress[] */
	public function findRecentByUser(string $userId, int $limit = 10): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->orderBy('updated_at', 'DESC')
			->setMaxResults($limit);
		$result = $qb->executeQuery();
		$rows = $result->fetchAllAssociative();
		$result->closeCursor();
		return array_map([$this, 'mapRowToEntity'], $rows);
	}
}
