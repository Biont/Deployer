<?php


namespace JordiLlonch\Component\Deployer\Exception;


use Exception;

class FileNotFoundException extends IOException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null, $directory = null)
    {
        if (null === $message) {
            if (null === $directory) {
                $message = 'File could not be found.';
            } else {
                $message = sprintf('File "%s" could not be found.', $directory);
            }
        }

        parent::__construct($message, $code, $previous);
    }

}

