<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller;

use OCA\Ereader\Service\FileService;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\StreamResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class FileApiController extends Controller {

	public function __construct(
		IRequest $request,
		private FileService $fileService,
	) {
		parent::__construct('ereader', $request);
	}

	#[NoAdminRequired]
	public function stream(int $fileId): StreamResponse|DataResponse {
		$file = $this->fileService->getFileById($fileId);
		if ($file === null) {
			return new DataResponse(['error' => 'Not found'], 404);
		}
		$stream = $file->fopen('rb');
		if ($stream === false) {
			return new DataResponse(['error' => 'Cannot open file'], 500);
		}
		$response = new StreamResponse($stream);
		$response->addHeader('Content-Type', $file->getMimeType());
		$response->addHeader('Content-Disposition', 'inline; filename="' . rawurlencode($file->getName()) . '"');
		$response->addHeader('Content-Length', (string) $file->getSize());
		return $response;
	}
}
