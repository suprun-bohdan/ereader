<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller\Api;

use OCA\Ereader\Service\ConfigService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\Attribute\CORS;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IUserSession;

class ConfigApiController extends ApiController {

	public function __construct(
		IRequest $request,
		private ConfigService $configService,
		private IUserSession $userSession,
	) {
		parent::__construct('ereader', $request);
	}

	#[CORS]
	#[NoAdminRequired]
	public function index(): DataResponse {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return new DataResponse(['books_folder' => ''], 401);
		}
		$booksFolder = $this->configService->getBooksFolder($user->getUID());
		return new DataResponse(['books_folder' => $booksFolder]);
	}

	#[CORS]
	#[NoAdminRequired]
	public function update(string $books_folder = ''): DataResponse {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return new DataResponse([], 401);
		}
		$this->configService->setBooksFolder($user->getUID(), $books_folder);
		return new DataResponse(['books_folder' => trim($books_folder, '/')]);
	}

	#[NoAdminRequired]
	public function preflighted_cors(string $path = ''): DataResponse {
		return new DataResponse();
	}
}
