<?php

namespace JordiLlonch\Component\Deployer\Deploy;


use JordiLlonch\Component\Deployer\Deploy\Operation\OperationsInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface DeployInterface extends OperationsInterface
{
    const EVENT_INITIALIZE_PRE = 'deploy.initialize.pre';
    const EVENT_INITIALIZE_POST = 'deploy.initialize.post';
    const EVENT_DOWNLOAD_PRE = 'deploy.download.pre';
    const EVENT_DOWNLOAD_ADAPT_CODE = 'deploy.download.adapt_code';
    const EVENT_DOWNLOAD_POST = 'deploy.download.post';
    const EVENT_CANCEL_DOWNLOAD_PRE = 'deploy.cancel_download.pre';
    const EVENT_CANCEL_DOWNLOAD_POST = 'deploy.cancel_download.post';
    const EVENT_CODE2PRODUCTION_PRE = 'deploy.code2production.pre';
    const EVENT_CODE2PRODUCTION_POST = 'deploy.code2production.post';
    const EVENT_CANCEL_CODE2PRODUCTION_PRE = 'deploy.cancel_code2production.pre';
    const EVENT_CANCEL_CODE2PRODUCTION_POST = 'deploy.cancel_code2production.post';
    const EVENT_ROLLBACK_PRE = 'deploy.rollback.pre';
    const EVENT_ROLLBACK_POST = 'deploy.rollback.post';
    const EVENT_CLEAN_PRE = 'deploy.clean.pre';
    const EVENT_CLEAN_POST = 'deploy.clean.post';
    const EVENT_SYNCRONIZE_PRE = 'deploy.syncronize.pre';
    const EVENT_SYNCRONIZE_POST = 'deploy.syncronize.post';

    const EVENT_EXECUTE = 'deploy.execute';

    public function getName();
}
