<?php


namespace JordiLlonch\Component\Deployer\Deploy\Event;


use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;
use Symfony\Component\EventDispatcher\Event;

class OperationEvent extends Event
{
    /**
     * @var BaseDeploy
     */
    protected $deploy;

    public function __construct(BaseDeploy $deploy)
    {
        $this->deploy = $deploy;
    }

    /**
     * @return BaseDeploy
     */
    public function getDeploy()
    {
        return $this->deploy;
    }
}