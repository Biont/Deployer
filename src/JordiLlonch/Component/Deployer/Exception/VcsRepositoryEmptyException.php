<?php


namespace JordiLlonch\Component\Deployer\Exception;


use Exception;

class VcsRepositoryEmptyException extends VcsException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null, $directory = null)
    {
        if (null === $message) {
            $message = 'VCS repository empty.';
        }

        parent::__construct($message, $code, $previous, $directory);
    }

}

