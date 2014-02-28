<?php


namespace JordiLlonch\Component\Deployer\Exception;


use Exception;

class PathNotFoundException extends IOException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null, $directory = null)
    {
        if (null === $message) {
            if (null === $directory) {
                $message = 'Path could not be found.';
            } else {
                $message = sprintf('Path "%s" could not be found.', $directory);
            }
        }

        parent::__construct($message, $code, $previous);
    }

}

