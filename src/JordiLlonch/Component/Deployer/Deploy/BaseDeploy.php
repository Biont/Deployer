<?php
/**
 * @author Jordi Llonch <llonch.jordi@gmail.com>
 * @date 24/09/13 17:59
 */

namespace JordiLlonch\Component\Deployer\Deploy;

use JordiLlonch\Component\Deployer\Deploy\Entity\Deploy;
use JordiLlonch\Component\Deployer\Deploy\Entity\DeployConfig;
use JordiLlonch\Component\Deployer\Deploy\CopyStrategy\CopyStrategyInterface;
use JordiLlonch\Component\Deployer\Deploy\Event\ExecuteEvent;
use JordiLlonch\Component\Deployer\Deploy\Event\OperationEvent;
use JordiLlonch\Component\Deployer\Deploy\Helper\HelperSet;
use JordiLlonch\Component\Deployer\Deploy\Operation\OperationsSet;
use JordiLlonch\Component\Deployer\Deploy\Repository\DeployRepositoryInterface;
use JordiLlonch\Component\Deployer\Deploy\Ssh\Broker;
use JordiLlonch\Component\Deployer\Deploy\Vcs\VcsInterface;
use JordiLlonch\Component\Deployer\Deploy\Process\Process;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class BaseDeploy implements DeployInterface
{
    /**
     * @var DeployConfig
     */
    private $config;

    /**
     * @var VcsInterface
     */
    private $vcs;

    /**
     * @var CopyStrategyInterface
     */
    private $copyStrategy;

    /**
     * @var Broker
     */
    private $sshBroker;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HelperSet
     */
    private $helperSet;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var DeployRepositoryInterface
     */
    private $deployRepository;

    /**
     * @var OperationsSet
     */
    private $operationsSet;

    /**
     * @var Deploy
     */
    private $deployEntity;

    public function __construct(
        EventDispatcherInterface $dispatcher, Process $process, DeployConfig $config, CopyStrategyInterface $copyStrategy, Broker $sshBroker, VcsInterface $vcs, DeployRepositoryInterface $deployRepository)
    {
        $this->logger = new NullLogger();
        $this->helperSet = new HelperSet(array());

        $this->config = $config;
        $this->vcs = $vcs;
        $this->copyStrategy = $copyStrategy;
        $this->sshBroker = $sshBroker;
        $this->dispatcher = $dispatcher;
        $this->process = $process;
        $this->deployRepository = $deployRepository;

        $this->setDefaultOperationSet();

        $this->deployEntity = $this->deployRepository->load();
    }

    /**
     * @return OperationsSet
     */
    public function getOperationsSet()
    {
        return $this->operationsSet;
    }

    public function setDefaultOperationSet(OperationsSet $operationSet = null)
    {
        if (is_null($operationSet)) $this->operationsSet = new OperationsSet($this);
        else $this->operationsSet = $operationSet;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \JordiLlonch\Component\Deployer\Deploy\Helper\HelperSet $helperSet
     */
    public function setHelperSet($helperSet)
    {
        $this->helperSet = $helperSet;
    }

    /**
     * @return \JordiLlonch\Component\Deployer\Deploy\Helper\HelperSet
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * @return \JordiLlonch\Component\Deployer\Deploy\Entity\DeployConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \JordiLlonch\Component\Deployer\Deploy\CopyStrategy\CopyStrategyInterface
     */
    public function getCopyStrategy()
    {
        return $this->copyStrategy;
    }

    /**
     * @return Broker
     */
    public function getSshBroker()
    {
        return $this->sshBroker;
    }

    /**
     * @return \JordiLlonch\Component\Deployer\Deploy\Vcs\VcsInterface
     */
    public function getVcs()
    {
        return $this->vcs;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->deployEntity->getCurrentVersion();
    }

    /**
     * @return string
     */
    public function getLastDownloadedVersion()
    {
        return $this->deployEntity->getLastDownloadedVersion();
    }

    /**
     * @return OperationEvent
     */
    public function createOperationEvent()
    {
        return new OperationEvent($this);
    }

    /**
     * Adds an event subscriber.
     *
     * @param EventSubscriberInterface $subscriber The subscriber.
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string $eventName The event to listen on
     * @param callable $listener The listener
     * @param integer $priority The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Get deployer name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->config->getName();
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    public function execute($command, $timeout = 60)
    {
        $process = null;
        if (!$this->config->isDryModeEnabled()) {
            $this->process->setLogger($this->logger);
            if ($this->config->isSudoEnabled()) $this->process->enableSudo();
            $process = $this->process->execute($command, $timeout);
            $this->dispatcher->dispatch(self::EVENT_EXECUTE, new ExecuteEvent($process));
        }

        return $process;
    }

    public function remoteExecute($command)
    {
        return $this->sshBroker->execute($command);
    }

    public function createNewVersion($hashFromLastVersionCode = null)
    {
        if (is_null($hashFromLastVersionCode)) {
            $hashFromLastVersionCode = $this->vcs->getHashFromLastVersionCode();
        }
        $version = date("Ymd_His") . '_' . $hashFromLastVersionCode;
        $this->setNewVersion($version);
        $this->logger->info(sprintf('New version created: %s', $this->deployEntity->getLastDownloadedVersion()));

        return $version;
    }

    public function setLastDownloadedVersionAsCurrentVersion()
    {
        $version = $this->deployEntity->getLastDownloadedVersion();
        $this->setCurrentVersion($version);
    }

    public function getLocalNewReleasePath()
    {
        return $this->config->getLocalReleasesPath() . DIRECTORY_SEPARATOR . $this->deployEntity->getLastDownloadedVersion();
    }

    public function getRemoteNewReleasePath()
    {
        return $this->config->getRemoteReleasesPath() . DIRECTORY_SEPARATOR . $this->deployEntity->getLastDownloadedVersion();
    }

    public function getRemoteCurrentReleasePath()
    {
        return $this->config->getRemoteReleasesPath() . DIRECTORY_SEPARATOR . $this->deployEntity->getCurrentVersion();
    }

    public function cloneCodeRepository()
    {
        $newReleasePath = $this->getLocalNewReleasePath();
        $this->vcs->cloneCodeRepository($newReleasePath);
    }

    public function syncronizeServers()
    {
        $newReleasePath = $this->getLocalNewReleasePath();
        $remoteReleasePath = $this->config->getRemoteReleasesPath();
        $this->copyStrategy->syncronize($newReleasePath, $remoteReleasePath);
    }

    public function dispatch($eventName, $event = null)
    {
        $this->dispatcher->dispatch($eventName, $event);
    }

    /**
     * @param $version
     */
    public function setNewVersion($version)
    {
        $this->deployEntity->setLastDownloadedVersion($version);
        $this->deployRepository->save($this->deployEntity);
    }

    /**
     * @param $version
     */
    public function setCurrentVersion($version)
    {
        $this->deployEntity->setCurrentVersion($version);
        $this->deployRepository->save($this->deployEntity);
        $this->logger->info(sprintf('Current version: %s', $this->deployEntity->getCurrentVersion()));
    }
}