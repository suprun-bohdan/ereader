<?php

declare(strict_types=1);

namespace OCA\Ereader\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method int getFileId()
 * @method void setFileId(int $fileId)
 * @method string getPosition()
 * @method void setPosition(string $position)
 * @method string|null getNote()
 * @method void setNote(?string $note)
 * @method string getCreatedAt()
 * @method void setCreatedAt(string $createdAt)
 */
class Bookmark extends Entity {

	protected ?string $userId = null;
	protected ?int $fileId = null;
	protected ?string $position = null;
	protected ?string $note = null;
	protected ?string $createdAt = null;

	public function __construct() {
		$this->addType('userId', 'string');
		$this->addType('fileId', 'integer');
		$this->addType('position', 'string');
		$this->addType('note', 'string');
		$this->addType('createdAt', 'string');
	}
}
