<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation;


use JordiLlonch\Component\Deployer\Deploy\Operation\Set\Clean;
use JordiLlonch\Component\Deployer\Deploy\Operation\Set\Code2production;
use JordiLlonch\Component\Deployer\Deploy\Operation\Set\Download;
use JordiLlonch\Component\Deployer\Deploy\Operation\Set\Initialize;
use JordiLlonch\Component\Deployer\Deploy\Operation\Set\Rollback;
use JordiLlonch\Component\Deployer\Deploy\Operation\Set\Syncronize;
use JordiLlonch\Component\Deployer\Deploy\BaseDeploy;

class OperationsSet implements OperationsInterface
{
    /**
     * @var BaseDeploy
     */
    protected $deploy;

    public function __construct(BaseDeploy $deploy)
    {
        $this->deploy = $deploy;
    }

    /**
     * @return Initialize
     */
    public function initialize()
    {
        return new Initialize($this->deploy);
    }

    /**
     * @return Download
     */
    public function download()
    {
        return new Download($this->deploy);
    }

    /**
     * @return Code2production
     */
    public function code2production()
    {
        return new Code2production($this->deploy);
    }

    /**
     * @param $version
     * @return Rollback
     */
    public function rollback($version = -1)
    {
        return new Rollback($this->deploy, $version);
    }

    /**
     * @param $numberOfDeploysToLeftOnClean
     * @return Clean
     */
    public function clean($numberOfDeploysToLeftOnClean)
    {
        return new Clean($this->deploy, $numberOfDeploysToLeftOnClean);
    }

    /**
     * @return Syncronize
     */
    public function syncronize()
    {
        return new Syncronize($this->deploy);
    }
}