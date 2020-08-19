<?php
namespace TechDivision\DocViewer\ViewHelpers;

use TechDivision\DocViewer\File\Node;
use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;
use TechDivision\DocViewer\Util;

/**
 * Renders a resource url by given packageKey and filePath
 */
class ResourceUrlViewHelper extends AbstractViewHelper
{
    /**
     * @Flow\Inject
     * @var \Neos\Flow\Package\PackageManager
     */
    protected $packageManager;

    /**
     * Initialize the arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('package', 'string', 'Package key for the resource', false, null);
        $this->registerArgument('filePath', 'string', 'File path for the resource', false, null);
    }

    /**
     * @return string
     */
    public function render()
    {

        $package = $this->arguments['package'];
        $filePath = $this->arguments['filePath'];
        return Util::buildResourceUrl(new Node($this->packageManager->getPackage($package), $filePath), null, $this->controllerContext->getRequest()->getHttpRequest()->getUri());
    }
}
