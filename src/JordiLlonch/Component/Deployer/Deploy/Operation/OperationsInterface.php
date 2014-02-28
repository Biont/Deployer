<?php


namespace JordiLlonch\Component\Deployer\Deploy\Operation;


interface OperationsInterface
{
    public function initialize();

    public function download();

    public function code2production();

    public function rollback($version = -1);

    public function clean($numberOfDeploysToLeftOnClean);

    public function syncronize();
}