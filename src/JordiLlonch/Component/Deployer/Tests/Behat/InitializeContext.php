<?php

namespace JordiLlonch\Component\Deployer\Tests\Behat;

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\PyStringNode;

class InitializeContext extends BehatContext
{
    private $base;

    public function __construct(BaseContext $base)
    {
        $this->base = $base;
    }

    /**
     * @When /^I run initialize$/
     */
    public function iRunInitialize()
    {
        $this->base->basicDeploy->initialize();
    }

    /**
     * @Then /^I should have a local directory structure$/
     */
    public function iShouldHaveALocalDirectoryStructure()
    {
        assertFileExists($this->base->rootTestPath . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'zone1' . DIRECTORY_SEPARATOR . 'releases');
        assertFileExists($this->base->rootTestPath . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'zone1' . DIRECTORY_SEPARATOR . 'data');
    }

    /**
     * @Given /^I should have a remote directory structure$/
     */
    public function iShouldHaveARemoteDirectoryStructure()
    {
        assertFileExists($this->base->rootTestPath . DIRECTORY_SEPARATOR . 'remote');
    }

    /**
     * @Then /^in log I should see "([^"]*)"$/
     */
    public function inLogIShouldSee($text)
    {
        $logs = $this->base->getMemoryStreamContent();
        assertContains($text, $logs);
    }

    /**
     * @Given /^I should have VCS proxy repository cloned$/
     */
    public function iShouldHaveVcsProxyRepositoryCloned()
    {
        assertFileExists($this->base->rootTestPath . DIRECTORY_SEPARATOR . 'vcs_proxy_repository' . DIRECTORY_SEPARATOR . '.git');
    }

    /**
     * @Given /^in log I should see subscriber events:$/
     */
    public function inLogIShouldSeeSubscriberEvents(PyStringNode $string)
    {
        foreach ($string->getLines() as $line) {
            $this->inLogIShouldSee($line);
        }
    }

    /**
     * @When /^I run initialize and throw an exception$/
     */
    public function iRunInitializeAndThrowAnException()
    {
        $throwException = false;
        try {
            $this->iRunInitialize();
        } catch (\Exception $e) {
            $throwException = true;
        }

        assertTrue($throwException, 'Initialize must throws an exception.');
    }
}
