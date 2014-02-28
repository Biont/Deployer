<?php

namespace JordiLlonch\Component\Deployer\Tests\Behat;


use Behat\Behat\Context\BehatContext;

class CleanContext extends BehatContext
{
    private $base;

    public function __construct(BaseContext $base)
    {
        $this->base = $base;
    }

    /**
     * @Given /^I create (\d+) fake releases$/
     */
    public function iCreateFakeReleases($numFakeReleases)
    {
        $localReleasesPath = $this->base->basicDeploy->getConfig()->getLocalReleasesPath();
        $remoteReleasesPath = $this->base->basicDeploy->getConfig()->getRemoteReleasesPath();
        for ($i = 1; $i <= $numFakeReleases; $i++) {
            mkdir(sprintf('%s%02d', $this->getFakePathPrefix($localReleasesPath), $i), 0777, true);
            mkdir(sprintf('%s%02d', $this->getFakePathPrefix($remoteReleasesPath), $i), 0777, true);
        }
    }

    /**
     * @When /^I run clean$/
     */
    public function iRunClean()
    {
        $this->base->basicDeploy->clean();
    }

    /**
     * @Given /^I should have only (\d+) from (\d+) releases in local server$/
     */
    public function iShouldHaveOnlyReleasesInLocalServer($numFakeReleases, $previousNumFakeReleases)
    {
        $releasePath = $this->base->basicDeploy->getConfig()->getLocalReleasesPath();
        $fakeFilePath = $this->getFakePathPrefix($releasePath);
        $this->assertReleasesExists($numFakeReleases, $previousNumFakeReleases, $fakeFilePath);
    }

    /**
     * @Given /^I should have only (\d+) from (\d+) releases in remote servers$/
     */
    public function iShouldHaveOnlyReleasesInRemoteServers($numFakeReleases, $previousNumFakeReleases)
    {
        $releasePath = $this->base->basicDeploy->getConfig()->getRemoteReleasesPath();
        $fakeFilePath = $this->getFakePathPrefix($releasePath);
        $this->assertReleasesExists($numFakeReleases, $previousNumFakeReleases, $fakeFilePath);
    }

    /**
     * @param $releasePath
     * @return string
     */
    private function getFakePathPrefix($releasePath)
    {
        return $releasePath . DIRECTORY_SEPARATOR . 'fake_';
    }

    /**
     * @param $numFakeReleases
     * @param $previousNumFakeReleases
     * @param $fakeFilePath
     */
    private function assertReleasesExists($numFakeReleases, $previousNumFakeReleases, $fakeFilePath)
    {
        for ($i = 1; $i >= $previousNumFakeReleases - $numFakeReleases; $i++) {
            assertFileNotExists(sprintf('%s%02d', $fakeFilePath, $i));
        }
        for ($i = $numFakeReleases + 1; $i <= $previousNumFakeReleases; $i++) {
            assertFileExists(sprintf('%s%02d', $fakeFilePath, $i));
        }
    }
}
