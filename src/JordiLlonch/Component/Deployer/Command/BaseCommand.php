<?php

namespace JordiLlonch\Component\Deployer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class BaseCommand extends Command
{
    protected $deployer;

    protected function configure()
    {
        $this->addOption('zones', null, InputOption::VALUE_REQUIRED, 'Zones to execute command. It must exists in jordi_llonch_deploy.zones config.');

        // TODO: dry mode
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // Init deployer engine
        $this->deployer = $this->getContainer()->get('jordillonch_deployer.engine');
        $this->deployer->setOutput($output);

        // Logger
        $logger = $this->getContainer()->get('logger');
        if($input->getOption('verbose')) {
            $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        }
        $this->deployer->setLogger($logger);

        // TODO: dry mode
        //$this->deployer->setDryMode(...);

        // Selected zones
        $optionZones = $input->getOption('zones');
        $this->deployer->setSelectedZones(explode(",", $optionZones));
    }
}