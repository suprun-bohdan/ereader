<?php

declare(strict_types=1);

namespace OCA\Ereader\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method int getFileId()
 * @method void setFileId(int $fileId)
 * @method string getProgressData()
 * @method void setProgressData(string $data)
 * @method string getUpdatedAt()
 * @method void setUpdatedAt(string $updatedAt)
 */
class Progress extends Entity {

	protected ?string $userId = null;
	protected ?int $fileId = null;
	protected ?string $progressData = null;
	protected ?string $updatedAt = null;

	public function __construct() {
		$this->addType('userId', 'string');
		$this->addType('fileId', 'integer');
		$this->addType('progressData', 'string');
		$this->addType('updatedAt', 'string');
	}
}
