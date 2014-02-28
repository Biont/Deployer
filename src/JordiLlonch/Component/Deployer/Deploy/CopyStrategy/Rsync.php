<?php
/**
 * @author Jordi Llonch <llonch.jordi@gmail.com>
 * @date 28/09/13 16:23
 */

namespace JordiLlonch\Component\Deployer\Deploy\CopyStrategy;


use JordiLlonch\Component\Deployer\Deploy\OperationSystem\Commands;
use JordiLlonch\Component\Deployer\Deploy\Entity\SshConfig;
use JordiLlonch\Component\Deployer\Deploy\Process\Process;

class Rsync implements CopyStrategyInterface
{
    protected $rsyncParameters;

    /**
     * @var Process;
     */
    protected $process;

    /**
     * @var Commands
     */
    protected $filesystemCommand;

    /**
     * @var Process[]
     */
    protected $servers = array();

    public function __construct(Process $process)
    {
        $this->process = $process;
        $this->filesystemCommand = new Commands();
    }

    /**
     * @param Commands $filesystemCommand
     */
    public function setFilesystemCommand($filesystemCommand)
    {
        $this->filesystemCommand = $filesystemCommand;
    }

    public function addServer(SshConfig $sshConfig)
    {
        $this->servers[] = $sshConfig;
    }

    public function syncronize($localPath, $remotePath)
    {
        // TODO: sync only new directories
        /**
         * @var SshConfig $sshConfig
         */
        foreach ($this->servers as $sshConfig) {
            $rsyncCommand = $this->filesystemCommand->rsyncSsh($localPath, $remotePath, $sshConfig, $this->rsyncParameters);
            $this->process->execute($rsyncCommand);
        }
    }

    /**
     * @param string $rsyncParameters
     */
    public function setRsyncParameters($rsyncParameters)
    {
        $this->rsyncParameters = $rsyncParameters;
    }

    /**
     * @return string
     */
    public function getRsyncParameters()
    {
        return $this->rsyncParameters;
    }
} 