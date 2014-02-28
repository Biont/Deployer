<?php


namespace JordiLlonch\Component\Deployer\Exception;


class RollbackStepBackwardExceededException extends \RuntimeException
{
    private $countAvailableStepBackwardVersions;

    public function __construct($countAvailableStepBackwardVersions, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->countAvailableStepBackwardVersions = $countAvailableStepBackwardVersions;
        if (null === $message) {
            $message = sprintf('There are only %d available versions to step backward.', $this->countAvailableStepBackwardVersions);
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getCountAvailableStepBackwardVersions()
    {
        return $this->countAvailableStepBackwardVersions;
    }
}
