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
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * @param string $package
	 * @param string $filePath
	 * @return mixed
	 */
	public function rawAction($package, $filePath) {

		if (!$this->accessManager->isPackageAccessable($package)) {
			throw new PackageNotAccessableException("You are not allowed to access the package " . $package);
		}

		$docDir = Util::getDocumentPath($this->packageManager->getPackage($package));
		$filePath = realpath($docDir . DIRECTORY_SEPARATOR . Parser::urlDecodeFilePath($filePath));

		// take care given file path is sub path of the doc dir of the package
		if(strpos($filePath, $docDir) === false) {
			throw new FileNotInsideDocumentationException("You are not allowed to access files outside the documentation folder");
		}

		$contentType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
		$this->response->setHeader("Content-Type", $contentType);

		return file_get_contents($filePath);

	}
}
