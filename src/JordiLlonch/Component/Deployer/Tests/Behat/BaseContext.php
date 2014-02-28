<?php


namespace JordiLlonch\Component\Deployer\Tests\Behat;

use JordiLlonch\Component\Deployer\Deploy\Basic;
use JordiLlonch\Component\Deployer\Deploy\Entity\DeployConfig;
use JordiLlonch\Component\Deployer\Deploy\Entity\SshConfig;
use JordiLlonch\Component\Deployer\Deploy\Entity\VcsConfig;
use JordiLlonch\Component\Deployer\Deploy\CopyStrategy\Rsync;
use JordiLlonch\Component\Deployer\Deploy\Helper\HelperSet;
use JordiLlonch\Component\Deployer\Deploy\Process\Process;
use JordiLlonch\Component\Deployer\Deploy\Repository\YamlDeployRepository;
use JordiLlonch\Component\Deployer\Deploy\Ssh\Broker;
use JordiLlonch\Component\Deployer\Deploy\Ssh\Client;
use JordiLlonch\Component\Deployer\Deploy\Ssh\Proxy\CliSsh;
use JordiLlonch\Component\Deployer\Deploy\Ssh\Proxy\PeclSsh2;
use JordiLlonch\Component\Deployer\Deploy\Vcs\Git;
use JordiLlonch\Component\Deployer\Tests\Behat\EventSubscriber\DeployerTestASubscriber;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BaseContext
{
    public $testServerName = 'testserver';
    public $rootTestPath;
    public $memoryStream;
    public $deployDataPath;
    public $versions = [];

    /**
     * @var Basic
     */
    public $basicDeploy;

    /**
     * Current system username
     * @var string
     */
    public $userName;

    /**
     * SSH public key path
     * @var string
     */
    public $sshPublicKeyPath;

    /**
     * SSH private key path
     * @var string
     */
    public $sshPrivateKeyPath;

    public function __construct()
    {
        $this->rootTestPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'deployer_test';
    }

    /**
     * @param array $hostServers
     * @return Basic
     */
    public function getBasicDeploy(array $hostServers)
    {
        $deployConfig = $this->getDeployConfig();
        $sshConfigServers = [];
        foreach ($hostServers as $hostServer) {
            $sshConfigServers[] = $this->getSshConfigServer($hostServer, 22, $this->userName, '~/.ssh/id_rsa.pub', '~/.ssh/id_rsa', false);
        }

        $logger = $this->getLogger();
        $sshBroker = $this->getBroker($sshConfigServers, $logger);
        $process = $this->getProcess($logger);
        $vcs = $this->getVcs($process);
        $copyStrategy = $this->getCopyStrategy($process, $sshConfigServers);
        $helperSet = $this->getHelperSet();
        $dispatcher = $this->getEventDispatcher();
        $deployRepository = $this->getDeployRepository();

        $basicDeploy = new Basic($dispatcher, $process, $deployConfig, $copyStrategy, $sshBroker, $vcs, $deployRepository);
        $basicDeploy->setLogger($logger);
        $basicDeploy->setHelperSet($helperSet);
        $basicDeploy->addSubscriber(new DeployerTestASubscriber());

        return $basicDeploy;
    }

    /**
     * @return VcsConfig
     */
    public function getVcsConfig()
    {
        $vcsConfig = new VcsConfig();
        $vcsConfig->setBranch('master');
        $vcsConfig->setUrl(sprintf('file://%s', $this->rootTestPath . DIRECTORY_SEPARATOR . 'origin_git_repository'));
        $vcsConfig->setProxyRepositoryPath($this->rootTestPath . DIRECTORY_SEPARATOR . 'vcs_proxy_repository');

        return $vcsConfig;
    }

    /**
     * @return array
     */
    public function getCustomConfig()
    {
        $customConfig = array(
            'custom1' => 'abc',
            'custom2' => 123
        );

        return $customConfig;
    }

    /**
     * @return array
     */
    public function getHelperConfig()
    {
        $helperConfig = array(
            'helper1' => 'abc',
            'helper2' => 123
        );

        return $helperConfig;
    }

    /**
     * @return DeployConfig
     */
    public function getDeployConfig()
    {
        $customConfig = $this->getCustomConfig();
        $helperConfig = $this->getHelperConfig();

        $deployConfig = new DeployConfig();
        $deployConfig->setName('zone1');
        $deployConfig->setLocalRootPath($this->rootTestPath . DIRECTORY_SEPARATOR . 'local');
        $deployConfig->setRemoteRootPath($this->rootTestPath . DIRECTORY_SEPARATOR . 'remote');
        $deployConfig->setEnvironment('dev');
        $deployConfig->setNumberOfDeploysToLeftOnClean(5);
        $deployConfig->setCustomConfig($customConfig);
        $deployConfig->setHelperConfig($helperConfig);
        $deployConfig->disableDryMode();
        $deployConfig->disableSudo();

        $deployConfig->setVersionCurrent('version_1');
        $deployConfig->setVersionNew('version_2');

        return $deployConfig;
    }

    /**
     * @param $host
     * @param $port
     * @param $user
     * @param $publicKeyFile
     * @param $privateKeyFile
     * @param $enableSudo
     * @return SshConfig
     */
    public function getSshConfigServer($host, $port, $user, $publicKeyFile, $privateKeyFile, $enableSudo)
    {
        $sshConfigServer = new SshConfig();
        $sshConfigServer->setHost($host);
        $sshConfigServer->setPort($port);
        $sshConfigServer->setUser($user);
        $sshConfigServer->setPublicKeyFile($publicKeyFile);
        $sshConfigServer->setPrivateKeyFile($privateKeyFile);
        if ($enableSudo) $sshConfigServer->enableSudo();
        else $sshConfigServer->disableSudo();

        return $sshConfigServer;
    }

    /**
     * @return string
     */
    public function getMemoryStreamContent()
    {
        rewind($this->memoryStream);
        $logs = stream_get_contents($this->memoryStream);

        return $logs;
    }

    public function initialize()
    {
        $this->createMemoryStream();
        $originGitRepositoryPath = $this->initializeFilesystem();
        $this->initializeGitRepository($originGitRepositoryPath);
    }

    public function cleanFilesystem()
    {
        exec(sprintf('rm -rf "%s"', $this->rootTestPath));
        $originGitRepositoryPath = $this->rootTestPath . DIRECTORY_SEPARATOR . 'origin_git_repository';
        exec(sprintf('rm -rf "%s"', $originGitRepositoryPath));
    }

    public function createMemoryStream()
    {
        $this->memoryStream = fopen('php://memory', 'rw');
    }

    /**
     * @return string
     */
    public function initializeFilesystem()
    {
        if (file_exists($this->rootTestPath)) {
            exec(sprintf('rm -rf "%s"', $this->rootTestPath));
        }
        mkdir($this->rootTestPath);

        $originGitRepositoryPath = $this->rootTestPath . DIRECTORY_SEPARATOR . 'origin_git_repository';
        mkdir($originGitRepositoryPath);

        return $originGitRepositoryPath;
    }

//    protected function getFakeFile()
//    {
//        $fakeDirectory = new vfsStreamDirectory('deploy_test_dir');
//        $fakeFile = vfsStream::newFile('fake_file')->withContent(serialize(array(1 =>  array('key2' => 'data_from_cache'))));
//        $fakeDirectory->addChild($fakeFile);
//        vfsStreamWrapper::register();
//        vfsStreamWrapper::setRoot($fakeDirectory);
//        $fakeFileUrl = vfsStream::url('fake_dir/fake_file');
//
//        return $fakeFileUrl;
//    }


    /**
     * @param $originGitRepositoryPath
     */
    public function initializeGitRepository($originGitRepositoryPath)
    {
        exec(sprintf('git init "%s"', $originGitRepositoryPath));
        exec(sprintf('touch "%s"', $originGitRepositoryPath . DIRECTORY_SEPARATOR . 'a'));
        exec(sprintf('touch "%s"', $originGitRepositoryPath . DIRECTORY_SEPARATOR . 'b'));
        exec(sprintf('touch "%s"', $originGitRepositoryPath . DIRECTORY_SEPARATOR . 'c'));
        exec(sprintf('git --git-dir="%s" --work-tree="%s" add a b c', $originGitRepositoryPath . DIRECTORY_SEPARATOR . '.git', $originGitRepositoryPath));
        exec(sprintf('git --git-dir="%s" --work-tree="%s" commit -m "first commit"', $originGitRepositoryPath . DIRECTORY_SEPARATOR . '.git', $originGitRepositoryPath));
    }

    /**
     * @param $path
     * @return mixed
     */
    public function filterHomePathCharacter($path)
    {
        $home = getenv("HOME");
        $path = str_replace('~', $home, $path);

        return $path;
    }

    public function closeMemoryStream()
    {
        fclose($this->memoryStream);
    }

    /**
     * @return Logger
     */
    private function getLogger()
    {
        $logger = new Logger('behat');
        $memoryHandler = new StreamHandler($this->memoryStream);
        $logPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'test.log';
        $fileHandler = new StreamHandler($logPath);
        $logger->pushHandler($memoryHandler);
        $logger->pushHandler($fileHandler);

        return $logger;
    }

    /**
     * @param array $sshConfigServers
     * @param $logger
     * @return Broker
     */
    private function getBroker(array $sshConfigServers, $logger)
    {
        $sshBroker = new Broker();
        $sshBroker->setLogger($logger);
        foreach ($sshConfigServers as $sshConfigServer) {
            $sshClientServer = new Client($sshConfigServer, new CliSsh());
            $sshClientServer->setLogger($logger);
            $sshBroker->addClient($sshClientServer);
        }

        // TODO: test PeclSsh2 because it is raising an error: terminated by signal SIGSEGV (Address boundary error) when executes ssh2_auth_pubkey_file

        return $sshBroker;
    }

    /**
     * @param $logger
     * @return Process
     */
    private function getProcess($logger)
    {
        $process = new Process();
        $process->setLogger($logger);

        return $process;
    }

    /**
     * @param $process
     * @internal param $vcsConfig
     * @return Git
     */
    private function getVcs($process)
    {
        $vcsConfig = $this->getVcsConfig();
        $vcs = new Git($vcsConfig, $process);

        return $vcs;
    }

    /**
     * @param $process
     * @param array $sshConfigServers
     * @return Rsync
     */
    private function getCopyStrategy($process, array $sshConfigServers)
    {
        $copyStrategy = new Rsync($process);
        foreach ($sshConfigServers as $sshConfigServer) {
            $copyStrategy->addServer($sshConfigServer);
        }

        return $copyStrategy;
    }

    /**
     * @return HelperSet
     */
    private function getHelperSet()
    {
        $helperSet = new HelperSet(array());

        return $helperSet;
    }

    /**
     * @return EventDispatcher
     */
    private function getEventDispatcher()
    {
        $dispatcher = new EventDispatcher();

        return $dispatcher;
    }

    /**
     * @return YamlDeployRepository
     */
    private function getDeployRepository()
    {
        $this->deployDataPath = $this->rootTestPath . DIRECTORY_SEPARATOR . 'deploy_data.yml';
        $deployRepository = new YamlDeployRepository($this->deployDataPath);

        return $deployRepository;
    }
} 