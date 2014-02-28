<?php


namespace JordiLlonch\Component\Deployer\Deploy\Repository;


use JordiLlonch\Component\Deployer\Deploy\Entity\Deploy;

interface DeployRepositoryInterface
{
    /**
     * @return Deploy
     */
    public function load();

    /**
     * @param Deploy $deploy
     */
    public function save(Deploy $deploy);
} 