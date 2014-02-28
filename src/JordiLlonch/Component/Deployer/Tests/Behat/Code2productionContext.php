<?php

namespace JordiLlonch\Component\Deployer\Tests\Behat;


use Behat\Behat\Context\BehatContext;
use JordiLlonch\Component\Deployer\Deploy\Basic;
use JordiLlonch\Component\Deployer\Deploy\Event\OperationEvent;
use Symfony\Component\Yaml\Yaml;

class Code2productionContext extends BehatContext
{
    private $base;

    private $previousVersion;

    public function __construct(BaseContext $base)
    {
        $this->base = $base;
    }

    /**
     * @Given /^I run code2production$/
     */
    public function iRunCodeproduction()
    {
        $this->base->basicDeploy->code2production();
        $this->base->versions[] = $this->base->basicDeploy->getCurrentVersion();
    }

    /**
     * @Given /^I should have a symbolic link created in remote servers$/
     */
    public function iShouldHaveASymbolicLinkCreatedInRemoteServers()
    {
        assertFileExists($this->base->basicDeploy->getConfig()->getRemoteCurrentPath());
        assertFileExists($this->base->basicDeploy->getConfig()->getRemoteCurrentPath() . DIRECTORY_SEPARATOR . 'a');
        assertFileExists($this->base->basicDeploy->getConfig()->getRemoteCurrentPath() . DIRECTORY_SEPARATOR . 'b');
        assertFileExists($this->base->basicDeploy->getConfig()->getRemoteCurrentPath() . DIRECTORY_SEPARATOR . 'c');

        $lastDownloadedVersion = $this->base->basicDeploy->getLastDownloadedVersion();
        $remoteCurrentPath = $this->base->basicDeploy->getConfig()->getRemoteCurrentPath();
        $linkTarget = readlink($remoteCurrentPath);
        assertContains($lastDownloadedVersion, $linkTarget);
    }

    /**
     * @Given /^I should have the new current version saved$/
     */
    public function iShouldHaveTheNewCurrentVersionSaved()
    {
        $content = Yaml::parse($this->base->deployDataPath);
        assertEquals($this->base->basicDeploy->getCurrentVersion(), $content['current_version']);
    }

    /**
     * @Given /^I run code2production and throws an exception$/
     */
    public function iRunCodeproductionAndThrowsAnException()
    {
        $this->base->basicDeploy->addListener(Basic::EVENT_CODE2PRODUCTION_POST, function (OperationEvent $event) {
            throw new \Exception('test fail post code2production');
        });

        $throwException = false;
        try {
            $this->previousVersion = $this->base->basicDeploy->getCurrentVersion();
            $this->iRunCodeproduction();
        } catch (\Exception $e) {
            $throwException = true;
        }

        assertTrue($throwException, 'Code2production must throws an exception.');
    }

    /**
     * @Then /^I should have a symbolic link created in remote servers linked to the first downloaded release$/
     */
    public function iShouldHaveASymbolicLinkCreatedInRemoteServersLinkedToTheFirstDownloadedRelease()
    {
        $linkTarget = readlink($this->base->basicDeploy->getConfig()->getRemoteCurrentPath());
        assertContains($this->previousVersion, $linkTarget);
    }

    /**
     * @Given /^I should have the first version saved as current version$/
     */
    public function iShouldHaveTheFirstVersionSavedAsCurrentVersion()
    {
        assertEquals($this->previousVersion, $this->base->basicDeploy->getCurrentVersion());
        $data = Yaml::parse($this->base->deployDataPath);
        assertEquals($this->previousVersion, $data['current_version']);
    }
}
