<?php
namespace TechDivision\DocViewer;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TYPO3\Flow\Annotations as Flow;

class Util {


	/**
	 * Directory of the documentation
	 * @var string
	 */
	protected static $docDir = 'Documentation';

	/**
	 * Get the documentation path
	 *
	 * @param \TYPO3\Flow\Package\PackageInterface $package
	 * @return string
	 */
	public static function getDocumentPath($package) {
		$path = $package->getPackagePath() . self::$docDir;
		if(!file_exists($path)) {
			return null;
		}
		return $path;
	}
}
