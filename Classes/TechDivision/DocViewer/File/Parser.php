<?php
namespace TechDivision\DocViewer\File;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\Exceptions\ParsingNotAllowedException;
use TYPO3\Flow\Annotations as Flow;

class Parser {

	/**
	 * Suffix for all resources to avid webserver's file delivering
	 * @var string
	 */
	protected static $resourceSuffix = '__docviwer';

	/**
	 * @var string
	 */
	protected $baseUri;

	public function __construct($baseUri) {
		$this->baseUri = $baseUri;
	}

	/**
	 * @param Node $node
	 * @return bool
	 */
	public function isAllowed($node) {

		$nodeInfo = $node->getInfo();

		if(is_array($nodeInfo) && isset($nodeInfo['extension'])) {
			return in_array($node->getInfo()['extension'], $this->markdownFileExtensions);
		}
		return false;
	}

	/**
	 * Files which are allowed for parsing as markdown
	 * @Flow\InjectConfiguration(path="parser.markdown.allowedFileExtensions")
	 * @var array
	 */
	protected $markdownFileExtensions;

	/**
	 * Encode a file path so ensure webserver configuration won't try to deliver a file itself
	 * @param string $path
	 * @return string
	 */
	public static function urlEncodeFilePath($path) {
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

	/**
	 * @param Node $node
	 * @param string $path
	 * @return string
	 */
	public static function buildResourceUrl($node, $path = null, $baseUri = '') {
		if(!$path) {
			$path = $node->getPath();
		}
		return $baseUri . 'techdivision-docviewer/' . $node->getPackageType() . "/" . $node->getPackageKey() . "/" . self::urlEncodeFilePath($path);
	}

	/**
	 * Replaces src value attributes in given dom string
	 * @param string $dom
	 * @param Node $node
	 * @return mixed
	 */
	protected function replaceSrcValues($dom, $node) {
		return preg_replace_callback(
			'/src\s*=\s*\"(.+?)\"/',
			function ($matches) use ($node) {
				$src = $matches[1];
				if(strpos($src, 'http') !== 0) {
					$src = self::buildResourceUrl($node, $src, $this->baseUri);
				}
				return 'src="' . $src . '"';
			},
			$dom);
	}

	/**
	 * Replaces href value attributes in given dom string
	 * @param string $dom
	 * @param Node $node
	 * @return mixed
	 */
	protected function replaceHrefValues($dom, $node) {
		return preg_replace_callback(
			'/href\s*=\s*\"(.+?)\"/',
			function ($matches) use ($node) {
				$href = $matches[1];
				if(strpos($href, 'http') !== 0) {
					$href = trim($href, "./");
					$href = 'show?moduleArguments%5BpackageKey%5D=' . $node->getPackageKey() . '&moduleArguments%5BpackageType%5D=' . $node->getPackageType() . '&moduleArguments%5BfilePath%5D=' . $href;
				}
				return 'href="' . $href . '"';
			},
			$dom);
	}

	/**
	 * Parse the given file
	 *
	 * @param Node $node
	 * @throws ParsingNotAllowedException
	 * @return string
	 */
	public function parseFile($node) {
		if(in_array($node->getInfo()['extension'], $this->markdownFileExtensions)) {
			$content = file_get_contents($node->getAbsolutePath());
			$parser = new \Parsedown();
			$dom = $parser->text($content);
			$dom = $this->replaceSrcValues($dom, $node);
			return $this->replaceHrefValues($dom, $node);
		} else {
			throw new ParsingNotAllowedException("Parsing of this file is not allowed. Allowed file types: " . join(", ", $this->markdownFileExtensions));
		}
	}
}
