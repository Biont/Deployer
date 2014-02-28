<?php


namespace JordiLlonch\Component\Deployer\Deploy;

/**
 * Class Basic
 * @package JordiLlonch\Component\Deployer\Deploy
 *
 */
class Basic extends BaseDeploy
{
    public function initialize()
    {
        $this->runOperation(__METHOD__, function () {
            $this->getOperationsSet()->initialize()->run();
        });
    }

    public function download()
    {
        $this->runOperation(__METHOD__, function () {
            $this->getOperationsSet()->download()->run();
        });
    }

    public function code2production()
    {
        $this->runOperation(__METHOD__, function () {
            $this->getOperationsSet()->code2production()->run();
        });
    }

    public function rollback($version = -1)
    {
        $this->runOperation(__METHOD__, function () use ($version) {
            $this->getOperationsSet()->rollback($version)->run();
        });
    }

    public function clean($numberOfDeploysToLeftOnClean = 10)
    {
        $numberOfDeploysToLeftOnClean = $this->getConfig()->getNumberOfDeploysToLeftOnClean();
        $this->runOperation(__METHOD__, function () use ($numberOfDeploysToLeftOnClean) {
            $this->getOperationsSet()->clean($numberOfDeploysToLeftOnClean)->run();
        });
    }

    public function syncronize()
    {
        $this->runOperation(__METHOD__, function () {
            $this->getOperationsSet()->syncronize()->run();
        });
    }

    /**
     * @param $method
     * @param $operation
     * @throws \Exception
     */
    private function runOperation($method, $operation)
    {
        try {
            $this->getLogger()->info($method);
            $operation();
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            throw $e;
        }
    }
}
