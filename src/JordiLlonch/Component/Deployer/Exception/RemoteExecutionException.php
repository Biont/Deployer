<?php


namespace JordiLlonch\Component\Deployer\Exception;


use Exception;

class RemoteExecutionException extends \RuntimeException implements ExecutionExceptionInterface
{
    private $command;

    public function __construct($message = null, $code = 0, Exception $previous = null, $command = null)
    {
        $this->command = $command;

        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        return $this->command;
    }
} 