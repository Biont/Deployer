<?php


namespace JordiLlonch\Component\Deployer\Deploy\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

class ExecuteEvent extends Event
{
    /**
     * @var Process
     */
    protected $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }
}