<?php


namespace JordiLlonch\Component\Deployer\Exception;


class IOException extends \RuntimeException implements IOExceptionInterface
{
    private $directory;

    public function __construct($message, $code = 0, \Exception $previous = null, $directory = null)
    {
        $this->directory = $directory;

        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->directory;
    }
}
