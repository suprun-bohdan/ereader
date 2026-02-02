<?php

declare(strict_types=1);

namespace OCA\Ereader\Dashboard;

use OCA\Ereader\Service\FileService;
use OCA\Ereader\Service\ProgressService;
use OCP\Dashboard\IWidget;
use OCP\IInitialStateService;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\Util;

class EreaderWidget implements IWidget {

	public function __construct(
		private IL10N $l10n,
		private IURLGenerator $urlGenerator,
		private IInitialStateService $initialStateService,
		private IUserSession $userSession,
		private ProgressService $progressService,
		private FileService $fileService,
	) {
	}

	public function getId(): string {
		return 'ereader_widget';
	}

	public function getTitle(): string {
		return $this->l10n->t('e-reader');
	}

	public function getOrder(): int {
		return 50;
	}

	public function getIconClass(): string {
		return 'icon-book';
	}

	public function getUrl(): ?string {
		return $this->urlGenerator->linkToRouteAbsolute('ereader.page.index');
	}

	public function load(): void {
		$user = $this->userSession->getUser();
		$appUrl = $this->urlGenerator->linkToRouteAbsolute('ereader.page.index');
		$recentBooks = [];

		if ($user !== null) {
			$recent = $this->progressService->getRecent(6);
			foreach ($recent as $item) {
				$fileId = (int) ($item['fileId'] ?? 0);
				if ($fileId <= 0) {
					continue;
				}
				$file = $this->fileService->getFileById($fileId);
				if ($file !== null) {
					$title = $file->getName();
					$recentBooks[] = [
						'fileId' => $fileId,
						'title' => $title,
						'readUrl' => $appUrl . '#/read/' . $fileId,
					];
				}
			}
		}

		$this->initialStateService->provideInitialState('ereader', 'dashboardData', [
			'recentBooks' => $recentBooks,
			'appUrl' => $appUrl,
		]);
		Util::addScript('ereader', 'dashboard');
		Util::addStyle('ereader', 'dashboard');
	}
}
