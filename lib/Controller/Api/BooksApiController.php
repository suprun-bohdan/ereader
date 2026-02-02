<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller\Api;

use OCA\Ereader\Service\BooksService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\Attribute\CORS;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class BooksApiController extends ApiController {

	public function __construct(
		IRequest $request,
		private BooksService $booksService,
	) {
		parent::__construct('ereader', $request);
	}

	#[CORS]
	#[NoAdminRequired]
	public function index(): DataResponse {
		$books = $this->booksService->listBooks();
		return new DataResponse($books);
	}
}
