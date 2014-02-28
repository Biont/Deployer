<?php


namespace JordiLlonch\Component\Deployer\Exception;


interface IOExceptionInterface
{
    /**
     * Returns the associated directory for the exception
     *
     * @return string The directory.
     */
    public function getPath();
}
