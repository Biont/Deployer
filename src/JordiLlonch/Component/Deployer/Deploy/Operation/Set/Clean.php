<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation\Set;

use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Clean extends BaseOperation
{
    private $numberOfDeploysToLeftOnClean;

    public function __construct(BaseDeploy $deploy, $numberOfDeploysToLeftOnClean)
    {
        parent::__construct($deploy);
        $this->numberOfDeploysToLeftOnClean = $numberOfDeploysToLeftOnClean;
    }

    protected function getPreEvent()
    {
        return BaseDeploy::EVENT_CLEAN_PRE;
    }

    protected function getPostEvent()
    {
        return BaseDeploy::EVENT_CLEAN_POST;
    }

    public function runOperation()
    {
        $this->deploy->getLogger()->info('Cleaning old deploys...');

        $finder = new Finder();
        $finder->in($this->deploy->getConfig()->getLocalReleasesPath());
        $finder->directories();
        $finder->sortByName();
        $finder->depth(0);
        $directoryList = array();
        /** @var SplFileInfo $file */
        foreach ($finder as $file) $directoryList[] = $file->getBaseName();

        while (count($directoryList) > $this->numberOfDeploysToLeftOnClean) {
            $path = array_shift($directoryList);
            $rmCommandLocal = $this->osCommands->removeRecursive($this->deploy->getConfig()->getLocalReleasesPath() . DIRECTORY_SEPARATOR . $path);
            $this->deploy->execute($rmCommandLocal);
            $rmCommandRemote = $this->osCommands->removeRecursive($this->deploy->getConfig()->getRemoteReleasesPath() . DIRECTORY_SEPARATOR . $path);
            $this->deploy->remoteExecute($rmCommandRemote);
        }
    }
} 