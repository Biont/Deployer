<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use JordiLlonch\Component\Deployer\Application\DeployerApp;
use JordiLlonch\Component\Deployer\DeployerKernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), getenv('SYMFONY_ENV') ?: 'dev');
$debug = getenv('DEPLOYER_DEBUG') !== '0' && !$input->hasParameterOption(array('--no-debug', '')) && $env !== 'prod';

$kernel = new DeployerKernel($env, $debug);
$consoleApp = new Application();
$application = new DeployerApp($kernel, $consoleApp);
