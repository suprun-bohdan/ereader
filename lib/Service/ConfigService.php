<?php

declare(strict_types=1);

namespace OCA\Ereader\Service;

use OCA\Ereader\AppInfo\Application;
use OCP\IConfig;

class ConfigService {

	public const KEY_BOOKS_FOLDER = 'books_folder';

	public function __construct(
		private IConfig $config,
	) {
	}

	public function getBooksFolder(string $userId): string {
		$value = $this->config->getUserValue($userId, Application::APP_ID, self::KEY_BOOKS_FOLDER, '');
		return $value === '' ? '' : trim($value, '/');
	}

	public function setBooksFolder(string $userId, string $path): void {
		$this->config->setUserValue($userId, Application::APP_ID, self::KEY_BOOKS_FOLDER, trim($path, '/'));
	}
}
