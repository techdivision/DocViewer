<?php
namespace TechDivision\DocViewer;

use Neos\Flow\Annotations as Flow;
use Neos\Neos\Controller\Module\AbstractModuleController;

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
     * @var \Neos\Flow\Package\PackageManager
     */
    protected $packageManager;

    /**
     * Determines if given package key should be accessable
     *
     * @param string $packageKey
     * @return bool
     */
    public function isPackageAccessable($packageKey) {
        return $this->packageManager->isPackageAvailable($packageKey) &&
        (!array_key_exists($packageKey, $this->packagesConfiguration['hide']) ||
            !$this->packagesConfiguration['hide'][$packageKey]
        );
    }

}
