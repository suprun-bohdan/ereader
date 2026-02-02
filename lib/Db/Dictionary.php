<?php

declare(strict_types=1);

namespace OCA\Ereader\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getWord()
 * @method void setWord(string $word)
 * @method string|null getTranslation()
 * @method void setTranslation(?string $translation)
 * @method string getCreatedAt()
 * @method void setCreatedAt(string $createdAt)
 * @method string getUpdatedAt()
 * @method void setUpdatedAt(string $updatedAt)
 */
class Dictionary extends Entity {

	protected ?string $userId = null;
	protected ?string $word = null;
	protected ?string $translation = null;
	protected ?string $createdAt = null;
	protected ?string $updatedAt = null;

	public function __construct() {
		$this->addType('userId', 'string');
		$this->addType('word', 'string');
		$this->addType('translation', 'string');
		$this->addType('createdAt', 'string');
		$this->addType('updatedAt', 'string');
	}
}
