<?php

namespace JordiLlonch\Component\Deployer\Tests\Behat;


use Behat\Behat\Context\BehatContext;
use JordiLlonch\Component\Deployer\Deploy\Basic;
use JordiLlonch\Component\Deployer\Deploy\Event\OperationEvent;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class DownloadContext extends BehatContext
{
    private $base;
    private $previousLastDownloadedVersion;

    public function __construct(BaseContext $base)
    {
        $this->base = $base;
    }

    /**
     * @Given /^I run download$/
     */
    public function iRunDownload()
    {
        $this->base->basicDeploy->download();
    }

    /**
     * @Given /^I should have code cloned in my local releases directory$/
     */
    public function iShouldHaveCodeClonedInMyLocalReleasesDirectory()
    {
        assertFileExists($this->base->basicDeploy->getLocalNewReleasePath() . DIRECTORY_SEPARATOR . 'a');
        assertFileExists($this->base->basicDeploy->getLocalNewReleasePath() . DIRECTORY_SEPARATOR . 'b');
        assertFileExists($this->base->basicDeploy->getLocalNewReleasePath() . DIRECTORY_SEPARATOR . 'c');
    }

    /**
     * @Given /^I should have code copied to my configured servers$/
     */
    public function iShouldHaveCodeCopiedToMyConfiguredServers()
    {
        assertFileExists($this->base->basicDeploy->getConfig()->getRemoteReleasesPath() . DIRECTORY_SEPARATOR . $this->base->basicDeploy->getLastDownloadedVersion() . DIRECTORY_SEPARATOR . 'a');
        assertFileExists($this->base->basicDeploy->getConfig()->getRemoteReleasesPath() . DIRECTORY_SEPARATOR . $this->base->basicDeploy->getLastDownloadedVersion() . DIRECTORY_SEPARATOR . 'b');
        assertFileExists($this->base->basicDeploy->getConfig()->getRemoteReleasesPath() . DIRECTORY_SEPARATOR . $this->base->basicDeploy->getLastDownloadedVersion() . DIRECTORY_SEPARATOR . 'c');
    }

    /**
     * @Given /^I have new downloaded version as last downloaded version$/
     */
    public function iHaveNewDownloadedVersionAsLastDownloadedVersion()
    {
        $data = Yaml::parse($this->base->deployDataPath);
        assertEquals($data['last_downloaded_version'], $this->base->basicDeploy->getLastDownloadedVersion());
    }

    /**
     * @Given /^I run download and throws an exception$/
     */
    public function iRunDownloadAndThrowsAnException()
    {
        $this->base->basicDeploy->addListener(Basic::EVENT_DOWNLOAD_POST, function (OperationEvent $event) {
            throw new \Exception('test fail post download');
        });

        $throwException = false;
        try {
            $this->previousLastDownloadedVersion = $this->base->basicDeploy->getLastDownloadedVersion();
            $this->iRunDownload();
        } catch (\Exception $e) {
            $throwException = true;
        }

        assertTrue($throwException, 'Download must throws an exception.');
    }

    /**
     * @Then /^I only have the first fake release in local releases directory$/
     */
    public function iOnlyHaveTheFirstFakeReleaseInLocalReleasesDirectory()
    {
        $releasePath = $this->base->basicDeploy->getConfig()->getLocalReleasesPath();
        $this->assertOnlyHaveTheFirtsFakeRelease($releasePath);
    }

    /**
     * @Given /^I only have the first fake release in my fake server$/
     */
    public function iOnlyHaveTheFirstFakeReleaseInMyFakeServer()
    {
        $releasePath = $this->base->basicDeploy->getConfig()->getRemoteReleasesPath();
        $this->assertOnlyHaveTheFirtsFakeRelease($releasePath);
    }

    /**
     * @Given /^I have previous last downloaded version$/
     */
    public function iHavePreviousLastDownloadedVersion()
    {
        assertEquals($this->previousLastDownloadedVersion, $this->base->basicDeploy->getLastDownloadedVersion());
        $data = Yaml::parse($this->base->deployDataPath);
        assertEquals($this->previousLastDownloadedVersion, $data['last_downloaded_version']);
    }

    /**
     * @param $releasePath
     */
    private function assertOnlyHaveTheFirtsFakeRelease($releasePath)
    {
        assertFileExists(sprintf('%s%02d', $this->getMainContext()->getFakePathPrefix($releasePath), 1));

        $finder = new Finder();
        $finder->in($releasePath);
        $finder->directories();
        assertEquals(1, $finder->count());
    }
}
