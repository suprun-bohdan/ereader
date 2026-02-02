<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller;

use OCA\Ereader\Service\ProgressService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\Attribute\CORS;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ProgressApiController extends ApiController {

	public function __construct(
		IRequest $request,
		private ProgressService $progressService,
	) {
		parent::__construct('ereader', $request);
	}

	#[CORS]
	#[NoAdminRequired]
	public function get(int $fileId): DataResponse {
		$progress = $this->progressService->getProgress($fileId);
		return new DataResponse($progress ?? []);
	}

	#[CORS]
	#[NoAdminRequired]
	public function save(int $fileId): DataResponse {
		$body = $this->request->getParams();
		$data = isset($body['data']) && is_array($body['data']) ? $body['data'] : [];
		$this->progressService->saveProgress($fileId, $data);
		return new DataResponse(['ok' => true]);
	}

	#[CORS]
	#[NoAdminRequired]
	public function recent(): DataResponse {
		$limit = (int) ($this->request->getParam('limit') ?? 10);
		$list = $this->progressService->getRecent($limit > 0 && $limit <= 50 ? $limit : 10);
		return new DataResponse($list);
	}
}
