<?php


namespace JordiLlonch\Component\Deployer\Deploy\Process;

use JordiLlonch\Component\Deployer\Exception\ExecutionException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process as SymfonyProcess;

class Process
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $sudoEnabled = false;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function enableSudo()
    {
        $this->sudoEnabled = true;
    }

    public function disableSudo()
    {
        $this->sudoEnabled = false;
    }

    /**
     * @return bool
     */
    public function isSudoEnabled()
    {
        return $this->sudoEnabled;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
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

    public function getProcess($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 3600, array $options = array())
    {
        return new SymfonyProcess($commandline, $cwd, $env, $stdin, $timeout, $options);
    }

    /**
     * @param $command
     * @param int $timeout
     * @return SymfonyProcess
     * @throws ExecutionException
     */
    public function execute($command, $timeout = 3600)
    {
        $command = $this->handleSudo($command);
        $this->logCommand($command);
        $process = $this->run($command, $timeout);
        $this->handleError($command, $process);
        $this->logOutput($process);
        $this->logOutputWarnings($process);

        return $process;
    }

    /**
     * @param $command
     * @return string
     */
    private function handleSudo($command)
    {
        if ($this->isSudoEnabled()) {
            $command = sprintf('sudo -n %s', $command);

            return $command;
        }

        return $command;
    }

    /**
     * @param $command
     * @param $timeout
     * @return SymfonyProcess
     */
    private function run($command, $timeout)
    {
        $process = $this->getProcess($command, null, null, null, $timeout);
        $process->setTimeout($timeout);
        $process->run();

        return $process;
    }

    /**
     * @param $command
     * @param $process
     * @throws \JordiLlonch\Component\Deployer\Exception\ExecutionException
     */
    private function handleError($command, SymfonyProcess $process)
    {
        if (!$process->isSuccessful()) {
            $this->logger->error(sprintf('Output: %s', $process->getErrorOutput()));
            throw new ExecutionException($process->getErrorOutput(), 0, null, $command);
        }
    }

    /**
     * @param $process
     */
    private function logOutput(SymfonyProcess $process)
    {
        $processOutput = $process->getOutput();
        if (!empty($processOutput)) {
            $this->logger->info(sprintf('Output: %s', $processOutput));
        }
    }

    /**
     * @param $process
     */
    private function logOutputWarnings(SymfonyProcess $process)
    {
        $processErrorOutput = $process->getErrorOutput();
        if (!empty($processErrorOutput)) {
            $this->logger->warning(sprintf('Output: %s', $processErrorOutput));
        }
    }

    /**
     * @param $command
     */
    private function logCommand($command)
    {
        $this->logger->info(sprintf('Process executes: %s', $command));
    }

} 