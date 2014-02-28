<?php

namespace JordiLlonch\Component\Deployer\Tests\Behat;


use Behat\Behat\Context\BehatContext;


class SyncronizeContext extends BehatContext
{
    private $base;

    public function __construct(BaseContext $base)
    {
        $this->base = $base;
    }

    /**
     * @When /^I run syncronize$/
     */
    public function iRunSyncronize()
    {
        $this->base->basicDeploy->syncronize();
    }
}
