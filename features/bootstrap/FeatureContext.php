<?php

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use JordiLlonch\Component\Deployer\Tests\Behat\BaseContext;
use JordiLlonch\Component\Deployer\Tests\Behat\CleanContext;
use JordiLlonch\Component\Deployer\Tests\Behat\Code2productionContext;
use JordiLlonch\Component\Deployer\Tests\Behat\DownloadContext;
use JordiLlonch\Component\Deployer\Tests\Behat\InitializeContext;
use JordiLlonch\Component\Deployer\Tests\Behat\RollbackContext;
use JordiLlonch\Component\Deployer\Tests\Behat\SyncronizeContext;

require_once __DIR__ . '/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';

class FeatureContext extends BehatContext
{
    private $base;

    public function __construct()
    {
        $this->base = new BaseContext();
        $this->useContext('initialize', new InitializeContext($this->base));
        $this->useContext('download', new DownloadContext($this->base));
        $this->useContext('code2production', new Code2productionContext($this->base));
        $this->useContext('rollback', new RollbackContext($this->base));
        $this->useContext('syncronize', new SyncronizeContext($this->base));
        $this->useContext('clean', new CleanContext($this->base));
    }

    /**
     * @BeforeScenario
     */
    public function prepareScenario(ScenarioEvent $event)
    {
        $this->base->cleanFilesystem();
        $this->base->initialize();
    }

    /**
     * @AfterScenario
     */
    public function cleanScenario(ScenarioEvent $event)
    {
        $this->base->cleanFilesystem();
        $this->base->closeMemoryStream();
    }

    /**
     * @Given /^my system checked and configured$/
     */
    public function mySystemCheckdAndConfigured()
    {
        $this->iHaveYourCurrentSystemUser();
        $this->iHaveYourSshPublicKeyPathIn('~/.ssh/id_rsa.pub');
        $this->iHaveYourSshPrivateKeyPathIn('~/.ssh/id_rsa');
        $this->yourSshdConfigAllowsAuthorizedkeysfile('/etc/sshd_config');
        $this->youHaveYourPublicKeyInFile('~/.ssh/authorized_keys');
        $this->iHaveAsAFakeDefinedInEtcHostsAs($this->base->testServerName . '1', 'server1', '127.0.0.1');
        $this->iHaveAsAFakeDefinedInEtcHostsAs($this->base->testServerName . '2', 'server2', '127.0.0.1');
    }

    public function iHaveYourCurrentSystemUser()
    {
        $informationAboutUser = posix_getpwuid(posix_geteuid());
        $this->base->userName = $informationAboutUser['name'];
    }

    public function iHaveYourSshPublicKeyPathIn($path)
    {
        $path = $this->base->filterHomePathCharacter($path);
        if (!file_exists($path)) throw new FileNotFoundException(sprintf('File not found: %s', $path));
        $this->base->sshPublicKeyPath = $path;
    }

    public function iHaveYourSshPrivateKeyPathIn($path)
    {
        $path = $this->base->filterHomePathCharacter($path);
        if (!file_exists($path)) throw new FileNotFoundException(sprintf('File not found: %s', $path));
        $this->base->sshPrivateKeyPath = $path;
    }

    public function yourSshdConfigAllowsAuthorizedkeysfile($path)
    {
        $sshdConfig = file_get_contents($path);
        if (!preg_match('/^AuthorizedKeysFile.*/m', $sshdConfig)) throw new \Exception('AuthorizedKeysFile directive not found.');
    }

    public function youHaveYourPublicKeyInFile($path)
    {
        $path = $this->base->filterHomePathCharacter($path);
        if (!file_exists($path)) throw new FileNotFoundException(sprintf('File not found: %s', $path));
        $publicKey = file_get_contents($this->base->sshPublicKeyPath);
        $authorizedKeys = file_get_contents($path);
        if (false === strpos($authorizedKeys, $publicKey)) throw new \Exception(sprintf('Your public key is not found in "%s"', $path));
    }

    public function iHaveAsAFakeDefinedInEtcHostsAs($serverName, $name, $ip)
    {
        $config = file_get_contents('/etc/hosts');
        if (!preg_match(sprintf('/^%s.*%s/m', $ip, $serverName), $config)) throw new \Exception(sprintf('Fake server "%s" not found in /etc/hosts.', $serverName));
    }

    /**
     * @Given /^a basic deploy with (\d+) fake server and (\d+) failing server$/
     */
    public function aBasicDeployWithFakeServerAndFailingServer($fakeServerNumber, $failingServerNumber)
    {
        $hostServers = [];
        for ($i=1; $i <= $fakeServerNumber; $i++) $hostServers[] = $this->base->testServerName . $i;
        for ($i=1; $i <= $failingServerNumber; $i++) $hostServers[] = 'fake_server_that_not_exists' . $i;
        $this->base->basicDeploy = $this->base->getBasicDeploy($hostServers);
    }

    /**
     * @Given /^a basic deploy with (\d+) servers$/
     */
    public function aBasicDeployWithServers($fakeServerNumber)
    {
        $this->aBasicDeployWithFakeServerAndFailingServer($fakeServerNumber, 0);
    }

    /**
     * @Given /^I create (\d+) fake local releases$/
     */
    public function iCreateFakeLocalReleases($numFakeReleases)
    {
        $releasePath = $this->base->basicDeploy->getConfig()->getLocalReleasesPath();
        for ($i = 1; $i <= $numFakeReleases; $i++) {
            mkdir(sprintf('%s%02d', $this->getFakePathPrefix($releasePath), $i), 0777, true);
        }
    }

    /**
     * @Given /^I should have the (\d+) fake releases in remote servers$/
     */
    public function iShouldHaveTheFakeReleasesInRemoteServers($numFakeReleases)
    {
        $releasePath = $this->base->basicDeploy->getConfig()->getRemoteReleasesPath();
        for ($i = 1; $i <= $numFakeReleases; $i++) {
            assertFileExists(sprintf('%s%02d', $this->getFakePathPrefix($releasePath), $i));
        }
    }

    /**
     * @param $releasePath
     * @return string
     */
    public function getFakePathPrefix($releasePath)
    {
        return $releasePath . DIRECTORY_SEPARATOR . 'fake_';
    }
}