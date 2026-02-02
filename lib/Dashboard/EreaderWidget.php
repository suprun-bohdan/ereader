<?php

declare(strict_types=1);

namespace OCA\Ereader\Dashboard;

use OCP\Dashboard\IWidget;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Util;

class EreaderWidget implements IWidget {

	public function __construct(
		private IL10N $l10n,
		private IURLGenerator $urlGenerator,
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
		Util::addScript('ereader', 'dashboard');
	}
}
