<?php

namespace JordiLlonch\Component\Deployer\Application;


use JordiLlonch\Component\Deployer\DeployerKernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployerApp
{
    const NAME = 'Deployer';
    const VERSION = 0.1;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var DeployerKernel
     */
    private $kernel;

    public function __construct(DeployerKernel $kernel, Application $application)
    {
        $this->kernel = $kernel;
        $this->application = $application;

        $this->application->setName(self::NAME);
        $this->application->setVersion(self::VERSION);

        $this->addConsoleCommands();
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return $this->application->run($input, $output);
    }

    private function addConsoleCommands()
    {
        $consoleServicesIds = $this->kernel->getConsoleServicesIds();
        foreach ($consoleServicesIds as $consoleServicesId => $attributes) {
            $this->application->add($this->kernel->get($consoleServicesId));
        }
    }
}
