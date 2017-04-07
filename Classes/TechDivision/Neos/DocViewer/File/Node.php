<?php
namespace TechDivision\Neos\DocViewer\File;

/*
 * This file is part of the TechDivision.Neos.DocViewer package.
 */
use TYPO3\Flow\Annotations as Flow;

class Node {


	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var boolean
	 */
	protected $isDir;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $absolutePath;

	/**
	 * @var array
	 */
	protected $info;

	/**
	 * @var array
	 */
	protected $content;

	/**
	 * @var boolean
	 */
	protected $active;

	/**
	 * Node constructor.
	 * @param $path
	 */
	public function __construct($path)
	{

		$this->name = basename($path);
		$this->isDir = is_dir($path);
		$this->absolutePath = realpath($path);

		if(!$this->isDir) {
			$this->info = pathinfo($path);
		}
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return boolean
	 */
	public function isIsDir()
	{
		return $this->isDir;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getAbsolutePath()
	{
		return $this->absolutePath;
	}

	/**
	 * @return array
	 */
	public function getInfo()
	{
		return $this->info;
	}

	/**
	 * @return array
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param array $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return boolean
	 */
	public function isActive()
	{
		return $this->active;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}

}
