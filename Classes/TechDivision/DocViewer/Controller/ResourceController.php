<?php
namespace TechDivision\DocViewer\Controller;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\Exceptions\FileNotInsideDocumentationException;
use TechDivision\DocViewer\Exceptions\PackageNotAccessableException;
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
	 * @var \TechDivision\DocViewer\AccessManager
	 */
	protected $accessManager;

	/**
	 * @param string $packageType
	 * @param string $packageKey
	 * @param string $filePath
	 * @return mixed
	 */
	public function rawAction($packageType, $packageKey, $filePath) {
		// @TODO fix for working Policy.yaml

		if (!$this->accessManager->isPackageAccessable($packageKey)) {
			throw new PackageNotAccessableException("You are not allowed to access the package " . $packageKey);
		}

		$docDir = Util::getDocumentPath($packageType, $packageKey);
		$filePath = realpath($docDir . DIRECTORY_SEPARATOR . Parser::urlDecodeFilePath($filePath));

		if(strpos($filePath, $docDir) === false) {
			throw new FileNotInsideDocumentationException("You are not allowed to acces files outside the documentation folder");
		}

		$contentType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
		$this->response->setHeader("Content-Type", $contentType);

		return file_get_contents($filePath);

	}
}
