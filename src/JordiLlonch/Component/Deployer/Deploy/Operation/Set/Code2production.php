<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation\Set;

use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;

class Code2production extends BaseOperation
{
    private $previousRemoteCurrentReleasePath;

    private $previousVersion;

    protected function getPreEvent()
    {
        return BaseDeploy::EVENT_CODE2PRODUCTION_PRE;
    }

    protected function getPostEvent()
    {
        return BaseDeploy::EVENT_CODE2PRODUCTION_POST;
    }

    protected function runOperation()
    {
        $this->previousRemoteCurrentReleasePath = $this->deploy->getRemoteCurrentReleasePath();
        $this->previousVersion = $this->deploy->getCurrentVersion();

        $sourcePath = $this->deploy->getRemoteNewReleasePath();
        $targetPath = $this->deploy->getConfig()->getRemoteCurrentPath();
        $this->deploy->remoteExecute($this->osCommands->createSymbolicLink($sourcePath, $targetPath));
        $this->deploy->setLastDownloadedVersionAsCurrentVersion();
        $this->dispatchEvent(BaseDeploy::EVENT_CODE2PRODUCTION_POST);
    }

    protected function runCancelOperation()
    {
        $event = $this->dispatchEvent(BaseDeploy::EVENT_CANCEL_CODE2PRODUCTION_PRE);
        if (!$event->isPropagationStopped()) {
            $this->cancelCode2production();
            $this->dispatchEvent(BaseDeploy::EVENT_CANCEL_CODE2PRODUCTION_POST);
        }
    }

    private function cancelCode2production()
    {
        $sourcePath = $this->previousRemoteCurrentReleasePath;
        $targetPath = $this->deploy->getConfig()->getRemoteCurrentPath();
        $this->deploy->remoteExecute($this->osCommands->createSymbolicLink($sourcePath, $targetPath));
        $this->deploy->setCurrentVersion($this->previousVersion);
        $this->previousRemoteCurrentReleasePath = null;
        $this->previousVersion = null;
    }
}