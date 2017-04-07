<?php
namespace TechDivision\Neos\DocViewer\Controller;

/*
 * This file is part of the TechDivision.Neos.DocViewer package.
 */
use TechDivision\Neos\DocViewer\File\Parser;
use TechDivision\Neos\DocViewer\Util;
use TYPO3\Flow\Annotations as Flow;

class ResourceController extends \TYPO3\Flow\Mvc\Controller\ActionController
{
	/**
	 * @param string $packageType
	 * @param string $packageKey
	 * @param string $filePath
	 * @return mixed
	 */
	public function rawAction($packageType, $packageKey, $filePath) {
		$docDir = Util::getDocumentPath($packageType, $packageKey);
		$filePath = $docDir . DIRECTORY_SEPARATOR . Parser::urlDecodeFilePath($filePath);
		$contentType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
		$this->response->setHeader("Content-Type", $contentType);

		return file_get_contents($filePath);
	}
}
