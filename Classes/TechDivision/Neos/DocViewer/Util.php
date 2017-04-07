<?php
namespace TechDivision\Neos\DocViewer;

/*
 * This file is part of the TechDivision.Neos.DocViewer package.
 */
use TYPO3\Flow\Annotations as Flow;

class Util {


	/**
	 * Get the documentation path
	 *
	 * @param string $packageType
	 * @param string $packageKey
	 * @return string
	 */
	public static function getDocumentPath($packageType, $packageKey) {
		$path = FLOW_PATH_PACKAGES . DIRECTORY_SEPARATOR . $packageType . DIRECTORY_SEPARATOR . $packageKey . '/Documentation';
		if(!file_exists($path)) {
			return null;
		}
		return $path;
	}
}
