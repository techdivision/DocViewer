<?php
namespace TechDivision\DocViewer;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Neos\Controller\Module\AbstractModuleController;

/**
 *
 * @Flow\Scope("singleton")
 */
class AccessManager extends AbstractModuleController
{

	/**
	 * Files which are used as entry files
	 * @Flow\InjectConfiguration("packages")
	 * @var array
	 */
	protected $packagesConfiguration;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * Determines if given package key should be accessable
	 *
	 * @param string $packageKey
	 * @return bool
	 */
	public function isPackageAccessable($packageKey) {
		return $this->packageManager->isPackageActive($packageKey) && !in_array($packageKey, $this->packagesConfiguration['hide']);
	}

}
