<?php
namespace TechDivision\DocViewer\File;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\Util;
use TYPO3\Flow\Annotations as Flow;

class Tree {


	/**
	 * @var Node
	 */
	protected $rootNode;

	/**
	 * Files which are used as entry files
	 * @Flow\InjectConfiguration("entryFiles")
	 * @var array
	 */
	protected $entryFiles;

	/**
	 * @var Parser
	 */
	protected $parser;

	public function __construct($packageType, $packageKey, $baseUri)
	{
		$this->parser = new Parser($baseUri);
		$this->rootNode = $this->buildFsNode($packageType, $packageKey);
	}

	/**
	 * Find file node by given path
	 *
	 * @param string $path
	 * @param Node $node
	 * @return Node | null
	 */
	public function findFileNodeByPath($path, $node = null) {
		if(!$node) {
			$node = $this->rootNode;
		}
		/** @var Node $childNode */
		foreach($node->getContent() as &$childNode) {

			if($childNode->getPath() == $path) {
				$childNode->setActive(true);
				return $childNode;
			}

			if($childNode->isIsDir()) {
				$found = $this->findFileNodeByPath($path, $childNode);
				if($found && $found->getPath() == $path) {
					$found->setActive(true);
					return $found;
				}
			}
		}
	}

	/**
	 * Finds an entry file in base dir
	 * @return bool|Node
	 */
	public function findEntryFile() {
		if(!$this->rootNode) {
			return null;
		}
		foreach($this->entryFiles as $allowedFileName) {
			/** @var Node $file */
			foreach($this->rootNode->getContent() as &$file) {
				if($file->isIsDir()) {
					continue;
				}
				if($file->getInfo()['filename'] == $allowedFileName) {
					$file->setActive(true);
					return $file;
				}
			}
		}
		return null;
	}

	public function getRootNode() {
		return $this->rootNode;
	}

	public function isDirectoryWithContent() {
		return ($this->rootNode && count($this->rootNode->getContent()) > 0);
	}

	/**
	 * Builds up given folder path as composite
	 * @param string $packageType
	 * @param string $packageKey
	 * @param string $path
	 * @return null|Node
	 */
	protected function buildFsNode($packageType, $packageKey, $path = null) {

		if(!$path) {
			$path = Util::getDocumentPath($packageType, $packageKey);
		}

		if(!file_exists($path)) {
			return null;
		}

		$node = new Node($packageType, $packageKey, $path);
		$node->setPath(trim(str_replace(Util::getDocumentPath($packageType, $packageKey), '', $path), "/"));
		if($node->isIsDir()) {

			$content = array();
			$rawContent = scandir($path);
			foreach($rawContent as $element) {
				// exclude dir itself and parent dir
				if($element == '.' || $element == '..') {
					continue;
				}
				$content[] = $this->buildFsNode($packageType, $packageKey, $path . DIRECTORY_SEPARATOR . $element);
			}
			$node->setContent($content);
		} else {
			$node->setIsParseable($this->parser->isAllowed($node));
		}
		return $node;
	}
}
