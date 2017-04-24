<?php
namespace TechDivision\DocViewer\Controller;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\Exceptions\FileNotInsideDocumentationException;
use TechDivision\DocViewer\Exceptions\PackageNotAccessibleException;
use TechDivision\DocViewer\File\Parser;
use TechDivision\DocViewer\Util;
use TYPO3\Flow\Annotations as Flow;

/**
 * Rudimentary service for resources. Flow does not allow serving static files which are not in the resource folder.
 * This controller serves only static files which are inside the documentation folder of the installed packages which
 * are not hidden by configuration.
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
	 * Serves static files
	 *
	 * @param string $package
	 * @param string $filePath
	 * @return mixed
	 */
	public function rawAction($package, $filePath) {

		// check if given package is valid
		if (!$this->accessManager->isPackageAccessable($package)) {
			throw new PackageNotAccessibleException("You are not allowed to access the package " . $package);
		}

		$docDir = Util::getDocumentPath($this->packageManager->getPackage($package));
		$filePath = realpath($docDir . DIRECTORY_SEPARATOR . Util::urlDecodeFilePath($filePath));

		// take care given file path is sub path of the doc dir of the package
		if(strpos($filePath, $docDir) < 0 || strpos($filePath, $docDir) === false) {
			throw new FileNotInsideDocumentationException("You are not allowed to access files outside the documentation folder");
		}

		$contentType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
		$this->response->setHeader("Content-Type", $contentType);

		return file_get_contents($filePath);

	}
}
