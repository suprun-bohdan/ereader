<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller\Api;

use OCA\Ereader\Service\FoldersService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\Attribute\CORS;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class FoldersApiController extends ApiController {

	public function __construct(
		IRequest $request,
		private FoldersService $foldersService,
	) {
		parent::__construct('ereader', $request);
	}

	#[CORS]
	#[NoAdminRequired]
	public function index(): DataResponse {
		$path = (string) ($this->request->getParam('path') ?? '');
		$folders = $this->foldersService->listFolders($path);
		return new DataResponse($folders);
	}
}
