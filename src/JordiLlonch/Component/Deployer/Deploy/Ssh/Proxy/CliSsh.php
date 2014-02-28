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

namespace JordiLlonch\Component\Deployer\Deploy\Ssh\Proxy;

use Symfony\Component\Process\Process;

class CliSsh extends BaseProxy
{
    protected $executable = 'ssh';
    protected $host;
    protected $port = 22;
    protected $user = '';
    protected $password = '';
    protected $privateKeyFile = null;
    protected $timeout = 600;

    private function canConnect()
    {
        $canConnect = false;
        if (0 == $this->execute('echo "connected"') && false !== strpos($this->lastOutput, 'connected')) {
            $canConnect = true;
        }

        return $canConnect;
    }

    private function assertConnected()
    {
        if (empty($this->host)) {
            throw new \Exception("You first need to connect");
        }
    }

    public function setExecutable($executable)
    {
        $this->executable = $executable;
    }

    public function connect($host, $port = 22)
    {
        $this->host = $host;
        $this->port = $port;

        return true;
    }

    public function authByPassword($user, $pwd)
    {
        throw new \Exception("Not implemented");
    }

    public function authByAgent($user)
    {
        $this->assertConnected();
        $this->user = $user;

        return $this->canConnect();
    }

    public function authByPublicKey($user, $public_key_file, $privateKeyFile, $pwd)
    {
        $this->assertConnected();
        $this->user = $user;
        $this->privateKeyFile = $privateKeyFile;

        return $this->canConnect();
    }

    public function execute($cmd)
    {
        $preparedCmd = $this->prepareCommand($cmd);
        if ($this->logger) $this->logger->info('preparedCmd: ' . $preparedCmd);

        $process = new Process($preparedCmd, null, null, null, $this->timeout);
        $process->run();
        $this->lastExitCode = $process->getExitCode();
        $this->lastOutput = $process->getOutput();
        $this->lastError = $process->getErrorOutput();

        return $process->getExitCode();
    }

    private function prepareCommand($cmd)
    {
        $user = $this->user ? '-l ' . $this->user : '';
        $keyFile = $this->privateKeyFile ? '-i ' . $this->privateKeyFile : '';

        return sprintf(
            "%s -t -t -o \"LogLevel=quiet\" -o \"UserKnownHostsFile=/dev/null\" -o \"StrictHostKeyChecking=no\" -p %s %s %s %s %s",
            $this->executable,
            $this->port,
            $keyFile,
            $user,
            $this->host,
            escapeshellarg($cmd));
    }
}