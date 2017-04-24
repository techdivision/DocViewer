<?php
namespace TechDivision\DocViewer;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\File\Node;
use TYPO3\Flow\Annotations as Flow;

class Util {


	/**
	 * Directory of the documentation
	 * @var string
	 */
	protected static $docDir = 'Documentation';

	/**
	 * Suffix for all resources to avid webserver's file delivering
	 * @var string
	 */
	public static $resourceSuffix = '__docviwer';

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

	/**
	 * @param Node $node
	 * @param string $path
	 * @return string
	 */
	public static function buildResourceUrl($node, $path = null, $baseUri = '') {
		if(!$path) {
			// if no path given the node is the resource url itself
			$path = $node->getPath();
		} else {
			// build paths for relative resources
			$sourcePathElements = explode("/", $node->getPath());
			array_pop($sourcePathElements);
			array_push($sourcePathElements, $path);
			$path = join("/", $sourcePathElements);
		}

		return $baseUri . 'techdivision-docviewer/' . $node->getPackageKey() . "/" . self::urlEncodeFilePath($path);
	}

	/**
	 * Encode a file path so ensure webserver configuration won't try to deliver a file itself
	 * @param string $path
	 * @return string
	 */
	protected static function urlEncodeFilePath($path) {
		return urlencode($path) . self::$resourceSuffix;
	}

	/**
	 * Decode a file path to ensure webserver configuration won't try to deliver a file itself
	 * @param string $path
	 * @return mixed
	 */
	public static function urlDecodeFilePath($path) {
		return preg_replace('/'. self::$resourceSuffix .'$/', '', urldecode($path));
	}
}
