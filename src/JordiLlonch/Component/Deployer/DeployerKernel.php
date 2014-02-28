<?php

namespace JordiLlonch\Component\Deployer;


use JordiLlonch\Component\Deployer\Infrastructure\DependencyInjection\DeployerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeployerKernel
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function __construct($env, $debug)
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('deploy_data_path', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'deployer_test' . DIRECTORY_SEPARATOR . 'deploy_data.yml');
        $extension = new DeployerExtension();
        $this->container->registerExtension($extension);
        $this->container->loadFromExtension($extension->getAlias());
        $this->container->compile();
    }

    /**
     * @param $id
     * @param int $invalidBehavior
     *
     * @return object
     */
    public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->container->get($id, $invalidBehavior);
    }

    /**
     * @return array
     */
    public function getConsoleServicesIds()
    {
        return $this->container->findTaggedServiceIds('deployer.command');
    }
} 