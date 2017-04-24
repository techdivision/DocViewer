<?php
namespace TechDivision\DocViewer\File;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\Exceptions\ParsingNotAllowedException;
use Neos\Flow\Annotations as Flow;
use TechDivision\DocViewer\Util;
use Neos\Flow\Mvc\Controller\ControllerContext;

class Parser {

	/**
	 * @var string
	 */
	protected $baseUri;

	/**
	 * @var ControllerContext
	 */
	protected $controllerContext;

	/**
	 * Files which are allowed for parsing as markdown
	 * @Flow\InjectConfiguration(path="parser.markdown.allowedFileExtensions")
	 * @var array
	 */
	protected $markdownFileExtensions;

	/**
	 * Parser constructor.
	 * @param string $baseUri
	 * @param ControllerContext $controllerContext
	 */
	public function __construct($baseUri, $controllerContext) {
		$this->baseUri = $baseUri;
		$this->controllerContext = $controllerContext;
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
					$src = Util::buildResourceUrl($node, $src, $this->baseUri);
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
		$uriBuilder = $uriBuilder = $this->controllerContext->getUriBuilder();
		return preg_replace_callback(
			'/href\s*=\s*\"(.+?)\"/',
			function ($matches) use ($node, $uriBuilder) {
				$href = $matches[1];
				if(strpos($href, 'http') !== 0) {
					$href = trim($href, "./");
					$uriBuilder->reset();
					$href = $uriBuilder->uriFor('show', array('package' => $node->getPackageKey(), 'filePath' => $href), null, null, null);
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
