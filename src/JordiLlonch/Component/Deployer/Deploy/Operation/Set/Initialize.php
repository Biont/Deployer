<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation\Set;


use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;
use JordiLlonch\Component\Deployer\Exception\PathNotFoundException;

/**
 * Class Initialize
 * @package JordiLlonch\Component\Deployer\Deploy\Operation
 *
 *
 * TODO: delete the following comment
 * `-- /var/www/my-app.com
 * |-- current → /var/www/my-app.com/releases/20100512131539
 * |-- releases
 * |   `-- 20100512131539
 * |   `-- 20100509150741
 * |   `-- 20100509145325
 * `-- shared
 * |-- web
 * |    `-- uploads
 * |-- log
 * `-- config
 * `-- databases.yml
 */
class Initialize extends BaseOperation
{
    protected function getPreEvent()
    {
        return BaseDeploy::EVENT_INITIALIZE_PRE;
    }

    protected function getPostEvent()
    {
        return BaseDeploy::EVENT_INITIALIZE_POST;
    }

    protected function runOperation()
    {
        $this->initializeLocalDirectories();
        $this->initializeRemoteDirectories();
        $this->initializeVcs();
    }

    protected function initializeLocalDirectories()
    {
        $this->createLocalDirectories();
        $this->checkExistsLocalDirectories();
    }

    protected function initializeRemoteDirectories()
    {
        $this->createRemoteDirectories();
        $this->checkExistsRemoteDirectories();
    }

    protected function initializeVcs()
    {
        $this->deploy->getVcs()->initialize();
    }

    protected function createLocalDirectories()
    {
        $this->deploy->execute($this->osCommands->mkdirRecursive($this->deploy->getConfig()->getLocalReleasesPath()));
        $this->deploy->execute($this->osCommands->mkdirRecursive($this->deploy->getConfig()->getLocalDataPath()));
    }

    protected function checkExistsLocalDirectories()
    {
        if (!is_dir($this->deploy->getConfig()->getLocalReleasesPath()))
            throw new PathNotFoundException(sprintf('Failed to initialize because "%s" local directory does not exist.', $this->deploy->getConfig()->getLocalReleasesPath()), 0, null, $this->deploy->getConfig()->getLocalReleasesPath());
        if (!is_dir($this->deploy->getConfig()->getLocalDataPath()))
            throw new PathNotFoundException(sprintf('Failed to initialize because "%s" local directory does not exist.', $this->deploy->getConfig()->getLocalDataPath()), 0, null, $this->deploy->getConfig()->getLocalDataPath());
    }

    protected function createRemoteDirectories()
    {
        $this->deploy->remoteExecute($this->osCommands->mkdirRecursive($this->deploy->getConfig()->getRemoteReleasesPath()));
        $this->deploy->remoteExecute($this->osCommands->mkdirRecursive($this->deploy->getConfig()->getRemoteSharedPath()));
        $this->deploy->remoteExecute($this->osCommands->chmod('a+rwx', $this->deploy->getConfig()->getRemoteReleasesPath()));
        $this->deploy->remoteExecute($this->osCommands->chmod('a+rwx', $this->deploy->getConfig()->getRemoteSharedPath()));
    }
    protected function checkExistsRemoteDirectories()
    {
        // If directory does not exist, remote ls will return exit code 1 raising a RemoteExecutionException.
        $this->deploy->remoteExecute($this->osCommands->ls($this->deploy->getConfig()->getRemoteReleasesPath()));
        $this->deploy->remoteExecute($this->osCommands->ls($this->deploy->getConfig()->getRemoteSharedPath()));
    }

}