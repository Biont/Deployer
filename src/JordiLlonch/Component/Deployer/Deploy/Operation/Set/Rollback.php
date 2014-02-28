<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation\Set;

use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;
use JordiLlonch\Component\Deployer\Exception\DeployVersionNotFoundException;
use JordiLlonch\Component\Deployer\Exception\RollbackStepBackwardExceededException;

class Rollback extends BaseOperation
{
    private $version;

    public function __construct(BaseDeploy $deploy, $version = -1)
    {
        parent::__construct($deploy);
        $this->version = $version;
    }

    protected function getPreEvent()
    {
        return BaseDeploy::EVENT_ROLLBACK_PRE;
    }

    protected function getPostEvent()
    {
        return BaseDeploy::EVENT_ROLLBACK_POST;
    }

    protected function runOperation()
    {
        $version = $this->handleStepBackwardVersion($this->version);
        $this->checkVersionExists($version);
        $this->rollback($version);
        $this->dispatchEvent(BaseDeploy::EVENT_ROLLBACK_POST);
    }

    /**
     * List of current downloaded versions of code
     * First last available version (without current)
     *
     * @param bool $removeCurrentVersion
     * @return array
     */
    protected function getVersionDirList($removeCurrentVersion = true)
    {
        // Get directory version list
        $dir = new \DirectoryIterator($this->deploy->getConfig()->getLocalReleasesPath());
        $arrListDir = array();
        foreach ($dir as $fileinfo) {
            /**
             * @var \SplFileInfo $fileinfo
             */
            if (!$fileinfo->isDot() && $fileinfo->isDir()) // also check if directory is the current one
            {
                $arrListDir[$fileinfo->__toString()] = $fileinfo->__toString();
            }
        }

        // Remove current version
        if ($removeCurrentVersion) {
            $currentVersion = $this->deploy->getCurrentVersion();
            $arrListDir = array_filter($arrListDir, function ($item) use ($currentVersion) {
                return $item != $currentVersion;
            });
        }

        krsort($arrListDir);
        $arrListDir = array_values($arrListDir);

        return $arrListDir;
    }

    /**
     * @param $version string Could be a negative integer to step backward or a concrete version.
     * @return mixed
     * @throws \JordiLlonch\Component\Deployer\Exception\RollbackStepBackwardExceededException
     * @throws \JordiLlonch\Component\Deployer\Exception\DeployVersionNotFoundException
     */
    private function handleStepBackwardVersion($version)
    {
        if (is_numeric($version)) {
            $versionStep = $version;
            $arrListDirWithCurrentVersion = $this->getVersionDirList(false);
            $keyCurrentVersion = array_search($this->deploy->getCurrentVersion(), $arrListDirWithCurrentVersion);
            if ($keyCurrentVersion === false) throw new DeployVersionNotFoundException($this->deploy->getCurrentVersion());
            $versionNum = $keyCurrentVersion - $versionStep;
            $arrListDirValues = array_values($arrListDirWithCurrentVersion);
            if (!isset($arrListDirValues[$versionNum])) throw new RollbackStepBackwardExceededException(count($arrListDirWithCurrentVersion));
            $version = $arrListDirValues[$versionNum];
        }

        return $version;
    }

    /**
     * @param $version
     * @throws \JordiLlonch\Component\Deployer\Exception\DeployVersionNotFoundException
     */
    private function checkVersionExists($version)
    {
        $arrListDir = $this->getVersionDirList();
        if (!in_array($version, $arrListDir)) throw new DeployVersionNotFoundException($version);
    }

    /**
     * @param $version
     */
    private function rollback($version)
    {
        $this->deploy->getLogger()->info('rolling back to version: ' . $version);
        $sourcePath = $this->deploy->getConfig()->getRemoteReleasesPath() . DIRECTORY_SEPARATOR . $version;
        $targetPath = $this->deploy->getConfig()->getRemoteCurrentPath();
        $this->deploy->remoteExecute($this->osCommands->createSymbolicLink($sourcePath, $targetPath));
    }
}