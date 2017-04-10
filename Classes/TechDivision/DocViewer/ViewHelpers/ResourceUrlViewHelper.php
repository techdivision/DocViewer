<?php
namespace TechDivision\DocViewer\ViewHelpers;

use TechDivision\DocViewer\File\Node;
use TechDivision\DocViewer\File\Parser;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Renders a resource url by given packageType, packageKey and filePath
 */
class ResourceUrlViewHelper extends AbstractViewHelper
{
	/**
	 * @param $packageType
	 * @param $packageKey
	 * @param $filePath
	 * @return string
	 */
    public function render($packageType, $packageKey, $filePath)
    {
        return Parser::buildResourceUrl(new Node($packageType, $packageKey, $filePath));
    }
}
