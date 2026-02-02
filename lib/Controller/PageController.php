<?php

declare(strict_types=1);

namespace OCA\Ereader\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
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
		$response->setContentSecurityPolicy(
			(new \OCP\AppFramework\Http\ContentSecurityPolicy())
				->addAllowedScriptDomain("'self'")
				->addAllowedScriptDomain("'unsafe-inline'")
				->addAllowedScriptDomain("blob:")
				->addAllowedStyleDomain("'self'")
				->addAllowedStyleDomain("'unsafe-inline'")
				->addAllowedConnectDomain("'self'")
				->addAllowedConnectDomain("data:")
		);
		return $response;
	}
}
