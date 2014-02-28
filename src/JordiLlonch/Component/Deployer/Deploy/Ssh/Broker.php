<?php

/**
 * This file is part of the JordiLlonchDeployBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace JordiLlonch\Component\Deployer\Deploy\Ssh;

use JordiLlonch\Component\Deployer\Exception\RemoteExecutionException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Broker
{
    protected $clients = array();

    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    public function __construct()
    {
        $this->setLogger(new NullLogger());
    }

    public function __destruct()
    {
        foreach ($this->clients as $sshClient) {
            $sshClient->disconnect();
        }

    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function addClient(Client $sshClient)
    {
        $this->clients[] = $sshClient;
    }

    public function execute($command)
    {
        /**
         * @var Client $client
         */
        $remoteExecutesResult = array();
        foreach ($this->clients as $client) {
            $client->execute($command);
            $remoteExecutesResult = $this->addResults($remoteExecutesResult, $client);
            $this->logResult($command, $client);
            $this->handleError($command, $client);
        }

        return $remoteExecutesResult;
    }

    /**
     * @param $remoteExecutesResult
     * @param $client
     * @return mixed
     */
    protected function addResults($remoteExecutesResult, $client)
    {
        // TODO: set what to return

        $server = $client->getHost() . ':' . $client->getPort();
//        $remoteExecutesResult[$server]['exit_code'] = $client->getLastExitCode();
        $remoteExecutesResult[$server]['output'] = $client->getLastOutput();

//        $remoteExecutesResult[$server]['error'] = $client->getLastError();
        return $remoteExecutesResult;
    }

    /**
     * @param $command
     * @param $client
     */
    protected function logResult($command, Client $client)
    {
        $lastOutput = $client->getLastOutput();
        $lastError = $client->getLastError();
        $this->getLogger()->info(sprintf('[%s:%s] remote exec: %s', $client->getHost(), $client->getPort(), $command));
        $this->getLogger()->info(sprintf('[%s:%s] remote exec result (exit code): %s', $client->getHost(), $client->getPort(), $client->getLastExitCode()));
        if (!empty($lastOutput)) $this->getLogger()->info(sprintf('[%s:%s] remote exec result (output): %s', $client->getHost(), $client->getPort(), $lastOutput));
        if (!empty($lastError)) $this->getLogger()->info(sprintf('[%s:%s] remote exec result (error): %s', $client->getHost(), $client->getPort(), $lastError));
    }

    /**
     * @param $command
     * @param $client
     * @throws \JordiLlonch\Component\Deployer\Exception\RemoteExecutionException
     */
    protected function handleError($command, $client)
    {
        if ($client->getLastExitCode() !== 0) {
            $errorMessage = $client->getLastError();
            if (empty($errorMessage)) $errorMessage = sprintf('Remote error executing: %s', $command);
            throw new RemoteExecutionException($errorMessage, $client->getLastExitCode(), null, $command);
        }
    }

//    protected function getSshClient($server)
//    {
//        if (isset($this->clients[$server])) {
//            if ($this->logger) $this->logger->debug('SshClient from cache (' . $server . ')');
//
//            return $this->clients[$server];
//        }
//
//        list($host, $port) = $this->extractHostPort($server);
//        $proxy = clone $this->proxy;
//        if ($host == 'localhost') $proxy = new Localhost();
//        $ssh = new Client($proxy);
//        if ($this->logger) $ssh->setLogger($this->logger);
//        $parameters = $this->parameters;
//        $parameters['ssh_port'] = $port;
//        $ssh->setParameters($parameters);
//        $ssh->setHost($host);
//        if ($this->logger) $this->logger->debug('SshClient connecting to ' . $server . '...');
//        $ssh->connect();
//        if ($this->logger) $this->logger->debug('SshClient connected');
//
//        $this->clients[$server] = $ssh;
//
//        return $ssh;
//    }
//
//    /**
//     * @param $server
//     * @return array
//     */
//    protected function extractHostPort($server)
//    {
//        $expServer = explode(':', $server);
//        $host = $expServer[0];
//        $port = 22;
//        if (isset($expServer[1])) $port = $expServer[1];
//
//        return array($host, $port);
//    }
}