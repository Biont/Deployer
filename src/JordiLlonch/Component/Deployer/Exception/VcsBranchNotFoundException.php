<?php


namespace JordiLlonch\Component\Deployer\Exception;


use Exception;

class VcsBranchNotFoundException extends IOException
{
    private $branch;

    public function __construct($message = null, $code = 0, \Exception $previous = null, $directory = null, $branch = null)
    {
        $this->branch = $branch;

        if (null === $message) {
            $message = 'VCS branch not found.';
        }

        parent::__construct($message, $code, $previous, $directory);
    }

    /**
     * Returns the associated branch for the exception
     *
     * @return string The branch.
     */
    public function getBranch()
    {
        return $this->branch;
    }
}

