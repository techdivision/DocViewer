<?php
namespace TechDivision\Neos\DocViewer\Controller;

/*
 * This file is part of the TechDivision.Neos.DocViewer package.
 */
use TechDivision\Neos\DocViewer\File\Parser;
use TechDivision\Neos\DocViewer\File\Tree;
use TechDivision\Neos\DocViewer\Util;
use TYPO3\Flow\Annotations as Flow;

class PackagesController extends \TYPO3\Flow\Mvc\Controller\ActionController
{

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

    /**
     * @return void
     */
    public function indexAction()
    {
		$packageGroups = array();
		foreach ($this->packageManager->getAvailablePackages() as $package) {
			/** @var Package $package */
			$packagePath = substr($package->getPackagepath(), strlen(FLOW_PATH_PACKAGES));
			$packageGroup = substr($packagePath, 0, strpos($packagePath, '/'));


			if($packageGroup == 'Application' || $packageGroup == 'Sites' || $packageGroup == 'Framework') {

				$tree = new Tree(Util::getDocumentPath($packageGroup, $package->getPackageKey()));

				$packageGroups[$packageGroup][$package->getPackageKey()] = array(
					'sanitizedPackageKey' => str_replace('.', '', $package->getPackageKey()),
					'version' => $package->getInstalledVersion(),
					'name' => $package->getComposerManifest('name'),
					'type' => $package->getComposerManifest('type'),
					'description' => $package->getPackageMetaData()->getDescription(),
					'metaData' => $package->getPackageMetaData(),
					'isActive' => $this->packageManager->isPackageActive($package->getPackageKey()),
					'isFrozen' => $this->packageManager->isPackageFrozen($package->getPackageKey()),
					'isProtected' => $package->isProtected(),
					'hasDoc' => $tree->isDirectoryWithContent()
				);
			}

		}

		$this->view->assign('packageGroups', $packageGroups);

	}

	/**
	 * @param string $packageKey
	 * @param string $packageType
	 * @param string $filePath
	 */
	public function showAction($packageKey, $packageType, $filePath = null) {
		$this->view->assign('packageKey', $packageKey);
		$this->view->assign('packageType', $packageType);

		$docDir = Util::getDocumentPath($packageType, $packageKey);

		$tree = new Tree($docDir);

		if($tree->isDirectoryWithContent()) {
			$this->addFlashMessage('No documention could be found');
		}
		$this->view->assign('directory', $tree->getRootNode());

		if($filePath) {
			$file = $tree->findFileNodeByPath($filePath);
		}else {
			$file = $tree->findEntryFile();
		}

		if($file) {
			$parser = new Parser($packageType, $packageKey);
			$this->view->assign('doc', $parser->parseFile($file));
		}
	}
}
