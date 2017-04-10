<?php
namespace TechDivision\DocViewer\Controller;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\File\Parser;
use TechDivision\DocViewer\Util;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Authorization\PrivilegeManagerInterface;

/**
 * Rudimentary service for resources
 *
 * @Flow\Scope("singleton")
 */
class ResourceController extends \TYPO3\Flow\Mvc\Controller\ActionController
{
	/**
	 * @Flow\Inject
	 * @var PrivilegeManagerInterface
	 */
	protected $privilegeManager;

	/**
	 * @param string $packageType
	 * @param string $packageKey
	 * @param string $filePath
	 * @return mixed
	 */
	public function rawAction($packageType, $packageKey, $filePath) {

		// @TODO check for visibility by given Settings.yaml
		// @TODO fix for working Policy.yaml

		$docDir = Util::getDocumentPath($packageType, $packageKey);
		$filePath = $docDir . DIRECTORY_SEPARATOR . Parser::urlDecodeFilePath($filePath);
		$contentType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
		$this->response->setHeader("Content-Type", $contentType);

		return file_get_contents($filePath);

	}
}
