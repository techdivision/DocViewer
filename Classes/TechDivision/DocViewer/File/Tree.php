<?php
namespace TechDivision\DocViewer\File;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\Util;
use Neos\Flow\Annotations as Flow;

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

	/**
	 * Tree constructor.
	 * @param \Neos\Flow\Package\PackageInterface $package
	 * @param $baseUri
	 */
	public function __construct(\Neos\Flow\Package\PackageInterface $package, $baseUri)
	{
		$this->parser = new Parser($baseUri);
		$this->rootNode = $this->buildFsNode($package);
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
		return null;
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
	 * @param \Neos\Flow\Package\PackageInterface $package
	 * @param string $path
	 * @return null|Node
	 */
	protected function buildFsNode(\Neos\Flow\Package\PackageInterface $package, $path = null) {

		if(!$path) {
			$path = Util::getDocumentPath($package);
		}

		if(!file_exists($path)) {
			return null;
		}

		$node = new Node($package, $path);
		if($node->isIsDir()) {

			$content = array();
			$rawContent = scandir($path);
			foreach($rawContent as $element) {
				// exclude dir itself and parent dir
				if($element == '.' || $element == '..') {
					continue;
				}
				$content[] = $this->buildFsNode($package, $path . DIRECTORY_SEPARATOR . $element);
			}
			$node->setContent($content);
		} else {
			$node->setIsParseable($this->parser->isAllowed($node));
		}
		return $node;
	}
}
