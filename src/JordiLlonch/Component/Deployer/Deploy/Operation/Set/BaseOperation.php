<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation\Set;


use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;
use JordiLlonch\Component\Deployer\Deploy\OperationSystem\Commands;
use JordiLlonch\Component\Deployer\Deploy\Event\OperationEvent;

abstract class BaseOperation implements OperationInterface
{
    /**
     * @var BaseDeploy
     */
    protected $deploy;

    /**
     * @var Commands
     */
    protected $osCommands;

    public function __construct(BaseDeploy $deploy)
    {
        $this->deploy = $deploy;
        $this->osCommands = new Commands();
    }

    /**
     * @param Commands $osCommands
     */
    public function setOsCommands($osCommands)
    {
        $this->osCommands = $osCommands;
    }

    public function run()
    {
        $event = $this->dispatchEvent($this->getPreEvent());
        if (!$event->isPropagationStopped()) {
            try {
                $this->runOperation();
                $this->dispatchEvent($this->getPostEvent());
            } catch (\Exception $e) {
                $this->deploy->getLogger()->error($e->getMessage());
                $this->runCancelOperation();
                throw $e;
            }
        }
    }

    abstract protected function getPreEvent();
    abstract protected function getPostEvent();
    abstract protected function runOperation();

    protected function runCancelOperation()
    {
    }

    /**
     * @param $eventName
     * @return OperationEvent
     */
    protected function dispatchEvent($eventName)
    {
        $this->deploy->dispatch($eventName, $event = $this->deploy->createOperationEvent());

        return $event;
    }
}