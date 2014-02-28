<?php


namespace JordiLlonch\Component\Deployer\Infrastructure\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DeployerExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
//        $configuration = new Configuration();
//        $processedConfig = $this->processConfiguration($configuration, $config);
//        $container->setParameter('jordi_llonch_deploy.config', $processedConfig['config']);
//        $container->setParameter('jordi_llonch_deploy.zones', $processedConfig['zones']);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/DependencyInjection']));
        $loader->load('services.yml');
    }
}