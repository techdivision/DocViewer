<?php
namespace TechDivision\Neos\DocViewer\File;

/*
 * This file is part of the TechDivision.Neos.DocViewer package.
 */
use TYPO3\Flow\Annotations as Flow;

class Parser {


	protected $packageType;

	protected $packageKey;

	public function __construct($packageType, $packageKey)
	{
		$this->packageType = $packageType;
		$this->packageKey = $packageKey;
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
		return urlencode($path) . '__docviwer';
	}

	/**
	 * Decode a file path to ensure webserver configuration won't try to deliver a file itself
	 * @param string $path
	 * @return mixed
	 */
	public static function urlDecodeFilePath($path) {
		return preg_replace('/__docviwer$/', '', urldecode($path));
	}

	/**
	 * Replaces src value attributes in given dom string
	 * @param string $dom
	 * @return mixed
	 */
	protected function replaceSrcValues($dom) {
		return preg_replace_callback(
			'/src\s*=\s*\"(.+?)\"/',
			function ($matches) {
				$src = $matches[1];
				$src = 'techdivision-docviewer/' . $this->packageType . "/" . $this->packageKey . "/" . self::urlEncodeFilePath($src);
				return 'src="' . $src . '"';
			},
			$dom);
	}

	/**
	 * Replaces href value attributes in given dom string
	 * @param string $dom
	 * @return mixed
	 */
	protected function replaceHrefValues($dom) {
		return preg_replace_callback(
			'/href\s*=\s*\"(.+?)\"/',
			function ($matches) {
				$href = $matches[1];
				if(strpos($href, 'http') !== 0) {
					$href = trim($href, "./");
					$href = 'neos/management/techDivisionNeosDocViewer/show?moduleArguments%5BpackageKey%5D=' . $this->packageKey . '&moduleArguments%5BpackageType%5D=' . $this->packageType . '&moduleArguments%5BfilePath%5D=' . $href;
				}
				return 'href="' . $href . '"';
			},
			$dom);
	}

	/**
	 * Parse the given file
	 *
	 * @param Node $node
	 * @return string
	 */
	public function parseFile($node) {
		if(in_array($node->getInfo()['extension'], $this->markdownFileExtensions)) {
			$content = file_get_contents($node->getAbsolutePath());
			$parser = new \Parsedown();
			$dom = $parser->text($content);
			$dom = $this->replaceSrcValues($dom);
			return $this->replaceHrefValues($dom);
		}
	}
}
