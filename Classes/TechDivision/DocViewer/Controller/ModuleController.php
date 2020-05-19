<?php
namespace TechDivision\DocViewer\Controller;

/*
 * This file is part of the TechDivision.DocViewer package.
 */
use TechDivision\DocViewer\Exceptions\PackageNotAccessibleException;
use TechDivision\DocViewer\Exceptions\ParsingNotAllowedException;
use TechDivision\DocViewer\File\Parser;
use TechDivision\DocViewer\File\Tree;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Controller\Module\AbstractModuleController;

/**
 *
 * @Flow\Scope("singleton")
 */
class ModuleController extends AbstractModuleController
{

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Package\PackageManager
     */
    protected $packageManager;

    /**
     * Files which are used as entry files
     * @Flow\InjectConfiguration("packages")
     * @var array
     */
    protected $packagesConfiguration;

    /**
     * @Flow\Inject
     * @var \TechDivision\DocViewer\AccessManager
     */
    protected $accessManager;

    /**
     * Routes to list or show action depending on configuration
     * @return void
     */
    public function indexAction() {
        if(isset($this->packagesConfiguration['entryPackage']) && $this->accessManager->isPackageAccessable($this->packagesConfiguration['entryPackage'])) {
            $this->forward('show', null, null, array('package' => $this->packagesConfiguration['entryPackage']));
        } else {
            $this->forward('list');
        }
    }

    /**
     * Lists packages with documentation depending on configuration
     * @return void
     */
    public function listAction() {

        $packageGroups = array();

        foreach($this->packagesConfiguration['visibleTypes'] as $type) {
            $packageGroups[$type] = array();
        }
        foreach ($this->packageManager->getAvailablePackages() as $package) {

            if(!$this->accessManager->isPackageAccessable($package->getPackageKey())) {
                continue;
            }

            /** @var Package $package */
            $packagePath = substr($package->getPackagepath(), strlen(FLOW_PATH_PACKAGES));
            $packageGroup = substr($packagePath, 0, strpos($packagePath, '/'));

            if(!in_array($packageGroup, $this->packagesConfiguration['visibleTypes'])) {
                continue;
            }

            $tree = new Tree($package, $this->controllerContext->getRequest()->getHttpRequest()->getBaseUri());

            if(!$tree->isDirectoryWithContent()) {
                continue;
            }

            $packageGroups[$packageGroup][$package->getPackageKey()] = array(
                'sanitizedPackageKey' => str_replace('.', '', $package->getPackageKey()),
                'version' => $package->getInstalledVersion(),
                'name' => $package->getComposerManifest('name'),
                'type' => $package->getComposerManifest('type'),
                //'description' => $package->getPackageMetaData()->getDescription()
                'description' => $package->getComposerManifest('description')
            );

        }

        $this->view->assign('projectVersion', $this->packageManager->getPackage('TechDivision.DocViewer')->getInstalledVersion());
        $this->view->assign('packageGroups', $packageGroups);
    }

    /**
     * Shows documentation of given package
     * @param string $package
     * @param string $filePath
     * @throws PackageNotAccessibleException
     * @return void
     */
    public function showAction($package, $filePath = null) {
        $baseUri = $this->controllerContext->getRequest()->getHttpRequest()->getBaseUri();

        if (!$this->accessManager->isPackageAccessable($package)) {
            throw new PackageNotAccessibleException("You are not allowed to access the package " . $package);
        }
        $package = $this->packageManager->getPackage($package);
        $this->view->assign('packageKey', $package->getPackageKey());

        $tree = new Tree($package, $baseUri);

        if(!$tree->isDirectoryWithContent()) {
            $this->addFlashMessage('No documention could be found');
        }
        $this->view->assign('node', $tree->getRootNode());

        if($filePath) {
            $file = $tree->findFileNodeByPath($filePath);
        }else {
            $file = $tree->findEntryFile();
        }

        if($file) {
            $parser = new Parser($baseUri, $this->getControllerContext());
            $this->view->assign('currentFile', $file);
            try {
                $documentContent = $parser->parseFile($file);
                $this->view->assign('doc', $documentContent);
            }catch (ParsingNotAllowedException $e) {
                $this->addFlashMessage($e->getMessage());
            }
        }
        $this->view->assign('projectVersion', $this->packageManager->getPackage('TechDivision.DocViewer')->getInstalledVersion());
    }
}
