<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller;

use OCA\Ereader\Service\DictionaryService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\Attribute\CORS;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class DictionaryApiController extends ApiController {

	public function __construct(
		IRequest $request,
		private DictionaryService $dictionaryService,
	) {
		parent::__construct('ereader', $request);
	}

	#[CORS]
	#[NoAdminRequired]
	public function index(): DataResponse {
		$list = $this->dictionaryService->list();
		return new DataResponse($list);
	}

	#[CORS]
	#[NoAdminRequired]
	public function create(): DataResponse {
		$word = $this->request->getParam('word');
		$word = is_string($word) ? trim($word) : '';
		$translation = $this->request->getParam('translation');
		$translation = is_string($translation) ? trim($translation) : null;
		try {
			$entry = $this->dictionaryService->add($word, $translation);
			return new DataResponse($entry);
		} catch (\InvalidArgumentException $e) {
			return new DataResponse(['error' => $e->getMessage()], 400);
		} catch (\Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], 400);
		}
	}

	#[CORS]
	#[NoAdminRequired]
	public function update(int $id): DataResponse {
		$translation = $this->request->getParam('translation');
		$translation = is_string($translation) ? trim($translation) : null;
		try {
			$entry = $this->dictionaryService->updateTranslation($id, $translation);
			return new DataResponse($entry);
		} catch (\Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], 400);
		}
	}

	#[CORS]
	#[NoAdminRequired]
	public function delete(int $id): DataResponse {
		try {
			$this->dictionaryService->delete($id);
			return new DataResponse(['ok' => true]);
		} catch (\Throwable $e) {
			return new DataResponse(['error' => $e->getMessage()], 400);
		}
	}
}
