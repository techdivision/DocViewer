<?php
namespace TechDivision\DocViewer\File;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\Exceptions\FileNotInsideDocumentationException;
use TechDivision\DocViewer\Util;
use Neos\Flow\Annotations as Flow;

class Node {


	/**
	 * @var string
	 */
	protected $name;


	/**
	 * @var \Neos\Flow\Package\PackageInterface $package
	 */
	protected $package;

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
	protected $info = array();

	/**
	 * @var array
	 */
	protected $content;

	/**
	 * @var boolean
	 */
	protected $active;

	/**
	 * @var boolean
	 */
	protected $isParseable;

	/**
	 * Node constructor.
	 * @param \Neos\Flow\Package\PackageInterface $package
	 * @param $path
	 */
	public function __construct(\Neos\Flow\Package\PackageInterface $package, $path)
	{
		$this->package = $package;
		$this->path = trim(str_replace(Util::getDocumentPath($package), '', $path), "/");
		$this->name = basename($path);
		$this->isDir = is_dir($path);
		$this->absolutePath = realpath($path);

		if(!$this->absolutePath) {
			return null;
		}
		if(strpos($this->absolutePath, Util::getDocumentPath($package)) === false) {
			throw new FileNotInsideDocumentationException("You are not allowed to acces files outside the documentation folder");
		}

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

	/**
	 * @return boolean
	 */
	public function isIsParseable()
	{
		return $this->isParseable;
	}

	/**
	 * @param boolean $isParseable
	 */
	public function setIsParseable($isParseable)
	{
		$this->isParseable = $isParseable;
	}

	/**
	 * @return string
	 */
	public function getPackageKey()
	{
		return $this->package->getPackageKey();
	}

}
