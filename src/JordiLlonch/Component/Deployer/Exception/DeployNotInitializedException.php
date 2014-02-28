<?php


namespace JordiLlonch\Component\Deployer\Exception;


class DeployNotInitializedException extends \RuntimeException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        if (null === $message) {
            $message = 'It seems deployer has not been initialized.';
        }

        parent::__construct($message, $code, $previous);
    }
}
