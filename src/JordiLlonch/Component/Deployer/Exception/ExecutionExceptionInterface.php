<?php


namespace JordiLlonch\Component\Deployer\Exception;


interface ExecutionExceptionInterface
{
    /**
     * Return the command executed.
     *
     * @return string
     */
    public function getCommand();
} 