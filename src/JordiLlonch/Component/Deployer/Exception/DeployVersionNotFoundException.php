<?php


namespace JordiLlonch\Component\Deployer\Exception;


class DeployVersionNotFoundException extends \RuntimeException
{
    private $version;

    public function __construct($version, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->version;
        if (null === $message) {
            $message = sprintf('"%s" version is not found in the available versions list.', $this->version);
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }
}
