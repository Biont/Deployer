<?php


namespace JordiLlonch\Component\Deployer\Exception;


interface VcsExceptionInterface
{
    /**
     * Returns the associated repository directory for the exception
     *
     * @return string The directory.
     */
    public function getRepositoryPath();
}
