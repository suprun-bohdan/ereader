<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller;

use OCA\Ereader\Service\BookmarkService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\Attribute\CORS;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class BookmarkApiController extends ApiController {

	public function __construct(
		IRequest $request,
		private BookmarkService $bookmarkService,
	) {
		parent::__construct('ereader', $request);
	}

	#[CORS]
	#[NoAdminRequired]
	public function index(int $fileId): DataResponse {
		$list = $this->bookmarkService->listByFile($fileId);
		return new DataResponse($list);
	}

	#[CORS]
	#[NoAdminRequired]
	public function create(int $fileId): DataResponse {
		$position = (string) ($this->request->getParam('position') ?? '');
		$note = $this->request->getParam('note');
		$note = is_string($note) ? $note : null;
		try {
			$bookmark = $this->bookmarkService->add($fileId, $position, $note);
			return new DataResponse($bookmark);
		} catch (\Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], 400);
		}
	}

	#[CORS]
	#[NoAdminRequired]
	public function delete(int $id): DataResponse {
		$this->bookmarkService->delete($id);
		return new DataResponse(['ok' => true]);
	}
}
