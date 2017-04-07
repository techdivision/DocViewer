<?php
namespace TechDivision\Neos\DocViewer\File;

/*
 * This file is part of the TechDivision.Neos.DocViewer package.
 */
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

	public function __construct($path)
	{
		$this->rootNode = $this->buildFsNode($path);
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
	 *
	 * @param $directory
	 */
	public function findEntryFile() {
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
	}

	public function getRootNode() {
		return $this->rootNode;
	}

	public function isDirectoryWithContent() {
		return ($this->rootNode && count($this->rootNode->getContent()) > 0);
	}

	/**
	 * Builds up given folder path as composite
	 * @param string $path
	 * @param $rootPath
	 * @return null|Node
	 */
	protected function buildFsNode($path, $rootPath = null) {

		if(!$rootPath) {
			$rootPath = $path;
		}

		if(!file_exists($path)) {
			return null;
		}

		$node = new Node($path);
		$node->setPath(trim(str_replace($rootPath, '', $path), "/"));
		if($node->isIsDir()) {

			$content = array();
			$rawContent = scandir($path);
			foreach($rawContent as $element) {
				// exclude dir itself and parent dir
				if($element == '.' || $element == '..') {
					continue;
				}
				$content[] = $this->buildFsNode($path . DIRECTORY_SEPARATOR . $element, $rootPath);
			}
			$node->setContent($content);
		}
		return $node;
	}
}
