<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation\Set;

use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;
use JordiLlonch\Component\Deployer\Exception\DeployNotInitializedException;

class Download extends BaseOperation
{
    private $previousVersion;

    protected function getPreEvent()
    {
        return BaseDeploy::EVENT_DOWNLOAD_PRE;
    }

    protected function getPostEvent()
    {
        return BaseDeploy::EVENT_DOWNLOAD_POST;
    }

    protected function runOperation()
    {
        $this->checkDeployInitialized();
        $this->previousVersion = $this->deploy->getCurrentVersion();
        $this->deploy->createNewVersion();
        $this->deploy->cloneCodeRepository();
        $this->dispatchEvent(BaseDeploy::EVENT_DOWNLOAD_ADAPT_CODE);
        $this->deploy->syncronizeServers();
    }

    protected function checkDeployInitialized()
    {
        if (!file_exists($this->deploy->getConfig()->getLocalReleasesPath())) throw new DeployNotInitializedException();
    }

    protected function runCancelOperation()
    {
        $event = $this->dispatchEvent(BaseDeploy::EVENT_CANCEL_DOWNLOAD_PRE);
        if (!$event->isPropagationStopped()) {
            $this->cancelDownload();
            $this->dispatchEvent(BaseDeploy::EVENT_CANCEL_DOWNLOAD_POST);
        }
    }

    private function cancelDownload()
    {
        $this->deploy->execute($this->osCommands->removeRecursive($this->deploy->getLocalNewReleasePath()));
        $this->deploy->remoteExecute($this->osCommands->removeRecursive($this->deploy->getRemoteNewReleasePath()));
        $this->deploy->setNewVersion($this->previousVersion);
        $this->previousVersion = null;
    }
}