<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class PageController extends Controller {

	public function __construct(
		IRequest $request,
	) {
		parent::__construct('ereader', $request);
	}

	#[NoAdminRequired]
	#[NoCSRFRequired]
	public function index(): TemplateResponse {
		$response = new TemplateResponse('ereader', 'main');
		// Allow blob: in iframe so PDF viewer can show stream via blob URL (avoids frame-ancestors on stream)
		$csp = new ContentSecurityPolicy();
		$csp->addAllowedFrameDomain('blob:');
		$response->setContentSecurityPolicy($csp);
		return $response;
	}
}
