<?php

/**
 * This file is part of the JordiLlonchDeployBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This class is based in Idephix - Automation and Deploy tool
 * https://github.com/ideatosrl/Idephix
 *
 */

namespace JordiLlonch\Component\Deployer\Deploy\Ssh;


use JordiLlonch\Component\Deployer\Deploy\Entity\SshConfig;
use JordiLlonch\Component\Deployer\Deploy\Ssh\Proxy\CliSsh;
use JordiLlonch\Component\Deployer\Deploy\Ssh\Proxy\PeclSsh2;
use JordiLlonch\Component\Deployer\Deploy\Ssh\Proxy\ProxyInterface;
use Psr\Log\LoggerInterface;

// TODO

class Client
{
    private $proxy;
    private $connected = false;

    /**
     * @var SshConfig
     */
    private $sshConfig;

    /**
     * Constructor
     *
     * @param \JordiLlonch\Component\Deployer\Deploy\Entity\SshConfig $sshConfig
     * @param ProxyInterface $proxy
     */
    public function __construct(SshConfig $sshConfig, ProxyInterface $proxy = null)
    {
        $this->sshConfig = $sshConfig;

        if (null === $proxy) {
            $proxy = function_exists('ssh2_auth_agent') ? new PeclSsh2() : new CliSsh();
        }
        $this->proxy = $proxy;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->proxy->setLogger($logger);
    }

    /**
     * @throws \Exception
     */
    public function connect()
    {
        $host = $this->getSshConfig()->getHost();
        $port = $this->getSshConfig()->getPort();
        $password = $this->getSshConfig()->getPassword();
        $user = $this->getSshConfig()->getUser();
        $publicKeyFile = $this->getSshConfig()->getPublicKeyFile();
        $privateKeyFile = $this->getSshConfig()->getPrivateKeyFile();

        if ($host === null) {
            throw new \Exception("You must set the host");
        }
        if (!$this->proxy->connect($host, $port)) {
            throw new \Exception("Unable to connect");
        }

        if (!empty($password) && !$this->proxy->authByPassword($user, $password)) {
            throw new \Exception("Unable to authenticate via password");
        }

        if (!empty($publicKeyFile) && !$this->proxy->authByPublicKey($user, $publicKeyFile, $privateKeyFile, $password)) {
            throw new \Exception("Unable to authenticate via public/private keys");
        }

        if (empty($password) && empty($publicKeyFile) && !$this->proxy->authByAgent($user)) {
            throw new \Exception("Unable to authenticate via agent");
        }

        $this->connected = true;

        return true;
    }

    public function disconnect()
    {
        $this->proxy->disconnect();
        $this->connected = false;
    }

    public function isConnected()
    {
        return $this->connected;
    }

    public function execute($command)
    {
        if (!$this->isConnected()) $this->connect();

        if ($this->getSshConfig()->isSudoEnabled()) {
            $command = 'sudo -n ' . $command;
        }

        return $this->proxy->execute($command);
    }

    public function getLastExitCode()
    {
        return $this->proxy->getLastExitCode();
    }

    public function getLastOutput()
    {
        return $this->proxy->getLastOutput();
    }

    public function getLastError()
    {
        return $this->proxy->getLastError();
    }

    public function getUser()
    {
        return $this->getSshConfig()->getUser();
    }

    public function getPort()
    {
        return $this->getSshConfig()->getPort();
    }

    public function getHost()
    {
        return $this->getSshConfig()->getHost();
    }

    /**
     * @return SshConfig
     */
    public function getSshConfig()
    {
        return $this->sshConfig;
    }
}