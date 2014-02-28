<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation\Set;

use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;

class Syncronize extends BaseOperation
{
    protected function getPreEvent()
    {
        return BaseDeploy::EVENT_SYNCRONIZE_PRE;
    }

    protected function getPostEvent()
    {
        return BaseDeploy::EVENT_SYNCRONIZE_POST;
    }

    protected function runOperation()
    {
        $this->deploy->syncronizeServers();
    }
}