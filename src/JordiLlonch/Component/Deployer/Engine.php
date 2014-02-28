<?php


namespace JordiLlonch\Component\Deployer;


use JordiLlonch\Component\Deployer\Deploy\Container;
use JordiLlonch\Component\Deployer\Deploy\DeployInterface;
use JordiLlonch\Component\Deployer\Deploy\Operation\OperationsInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Engine implements OperationsInterface
{
    /**
     * @var Deploy\Container
     */
    protected $deploysContainer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Container $container)
    {
        $this->logger = new NullLogger();
        $this->deploysContainer = $container;
    }

    public function initialize()
    {
        $operation = $this->operationInitialize();
        $this->runOperationOnAllDeploys($operation);
    }

    public function download()
    {
        $operation = $this->operationDownload();
        $operationOnError = $this->operationCancelDownload();
        $this->runOperationOnAllDeploys($operation, $operationOnError);
    }

    public function code2production()
    {
        $operation = $this->operationCode2production();
        $operationOnError = $this->operationCancelCode2production();
        $this->runOperationOnAllDeploys($operation, $operationOnError);
    }

    public function rollback()
    {
        $operation = $this->operationRollback();
        $this->runOperationOnAllDeploys($operation);
    }

    public function clean()
    {
        $operation = $this->operationClean();
        $this->runOperationOnAllDeploys($operation);
    }

    public function syncronize()
    {
        $operation = $this->operationSyncronize();
        $this->runOperationOnAllDeploys($operation);
    }

    /**
     * @param callable $operation
     * @param callable $operationOnError
     * @internal param $cancelOperation
     */
    protected function runOperationOnAllDeploys(\Closure $operation, \Closure $operationOnError = null)
    {
        try {
            foreach ($this->deploysContainer as $deploy) {
                $operation($deploy);
            }
        } catch (\Exception $e) {
            if (!is_null($operationOnError)) {
                foreach ($this->deploysContainer as $deploy) {
                    $operationOnError($deploy);
                }
            }
        }
    }

    /**
     * @return callable
     */
    protected function operationInitialize()
    {
        return function (DeployInterface $deploy) {
            $deploy->initialize();
        };
    }

    /**
     * @return callable
     */
    protected function operationDownload()
    {
        return function (DeployInterface $deploy) {
            $deploy->download();
        };
    }

    /**
     * @return callable
     */
    protected function operationCancelDownload()
    {
        $operationOnError = function (DeployInterface $deploy) {
            $deploy->cancelDownload();
        };
    }

    /**
     * @return callable
     */
    protected function operationCode2production()
    {
        return function (DeployInterface $deploy) {
            $deploy->code2production();
        };
    }

    /**
     * @return callable
     */
    protected function operationCancelCode2production()
    {
        $operationOnError = function (DeployInterface $deploy) {
            $deploy->cancelCode2production();
        };
    }

    /**
     * @return callable
     */
    protected function operationRollback()
    {
        return function (DeployInterface $deploy) {
            $deploy->rollback();
        };
    }

    /**
     * @return callable
     */
    protected function operationClean()
    {
        return function (DeployInterface $deploy) {
            $deploy->clean();
        };
    }

    /**
     * @return callable
     */
    protected function operationSyncronize()
    {
        return function (DeployInterface $deploy) {
            $deploy->syncronize();
        };
    }


} 