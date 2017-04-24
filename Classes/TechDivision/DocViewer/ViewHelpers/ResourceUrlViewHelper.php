<?php
namespace TechDivision\DocViewer\ViewHelpers;

use TechDivision\DocViewer\File\Node;
use TechDivision\DocViewer\Util;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Renders a resource url by given packageKey and filePath
 */
class ResourceUrlViewHelper extends AbstractViewHelper
{
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * @param string $package
	 * @param string $filePath
	 * @return string
	 */
    public function render($package, $filePath)
    {
        return Util::buildResourceUrl(new Node($this->packageManager->getPackage($package), $filePath), null, $this->controllerContext->getRequest()->getHttpRequest()->getBaseUri());
    }
}
