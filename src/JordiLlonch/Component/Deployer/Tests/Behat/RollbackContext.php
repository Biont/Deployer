<?php

namespace JordiLlonch\Component\Deployer\Tests\Behat;


use Behat\Behat\Context\BehatContext;


class RollbackContext extends BehatContext
{
    private $base;

    public function __construct(BaseContext $base)
    {
        $this->base = $base;
    }

    /**
     * @Given /^I run rollback$/
     */
    public function iRunRollback()
    {
        $this->base->basicDeploy->rollback();
    }

    /**
     * @Given /^I should have a symbolic link to first code version$/
     */
    public function iShouldHaveASymbolicLinkToFirstCodeVersion()
    {
        $firstVersion = $this->base->versions[0];
        $remoteCurrentPath = $this->base->basicDeploy->getConfig()->getRemoteCurrentPath();
        $linkTarget = readlink($remoteCurrentPath);
        assertContains($firstVersion, $linkTarget);
    }

    /**
     * @Given /^I wait (\d+) second$/
     */
    public function iWaitSecond($seconds)
    {
        sleep($seconds);
    }
}
