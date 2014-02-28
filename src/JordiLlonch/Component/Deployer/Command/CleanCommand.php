<?php

namespace JordiLlonch\Component\Deployer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class CleanCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('deployer:clean')
            ->setDescription('Remove old code. Left <info>clean_max_deploys</info> deploys.')
            ->setHelp(<<<EOT
The <info>deployer:clean</info> removes old code. Left <info>clean_max_deploys</info> deploys..
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $this->deployer->runClean();
    }
}