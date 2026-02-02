<?php

declare(strict_types=1);

namespace OCA\Ereader\Db;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class BookmarkMapper extends QBMapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'ereader_bookmarks', Bookmark::class);
	}

	/** @return Bookmark[] */
	public function findByUserAndFile(string $userId, int $fileId): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->andWhere($qb->expr()->eq('file_id', $qb->createNamedParameter($fileId, IQueryBuilder::PARAM_INT)))
			->orderBy('created_at', 'ASC');
		$result = $qb->executeQuery();
		$rows = $result->fetchAllAssociative();
		$result->closeCursor();
		return array_map([$this, 'mapRowToEntity'], $rows);
	}
}
