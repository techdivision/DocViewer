<?php
namespace TechDivision\DocViewer\ViewHelpers;

use TechDivision\DocViewer\File\Node;
use TechDivision\DocViewer\File\Parser;
use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

/**
 * Renders a resource url by given packageKey and filePath
 */
class ResourceUrlViewHelper extends AbstractViewHelper
{
	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * @param string $package
	 * @param string $filePath
	 * @return string
	 */
    public function render($package, $filePath)
    {
        return Parser::buildResourceUrl(new Node($this->packageManager->getPackage($package), $filePath), null, $this->controllerContext->getRequest()->getHttpRequest()->getBaseUri());
    }
}
