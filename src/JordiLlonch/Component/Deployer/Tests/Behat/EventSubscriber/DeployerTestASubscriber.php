<?php

namespace JordiLlonch\Component\Deployer\Tests\Behat\EventSubscriber;


use JordiLlonch\Component\Deployer\Deploy\Basic;
use JordiLlonch\Component\Deployer\Deploy\Event\ExecuteEvent;
use JordiLlonch\Component\Deployer\Deploy\Event\OperationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeployerTestASubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            Basic::EVENT_INITIALIZE_PRE => array('onInitializePre', 0),
            Basic::EVENT_INITIALIZE_POST => array('onInitializePost', 0),
            Basic::EVENT_DOWNLOAD_PRE => array('onDownloadPre', 0),
            Basic::EVENT_DOWNLOAD_ADAPT_CODE => array('onDownloadAdaptCode', 0),
            Basic::EVENT_DOWNLOAD_POST => array('onDownloadPost', 0),
            Basic::EVENT_CODE2PRODUCTION_PRE => array('onCode2productionPre', 0),
            Basic::EVENT_CODE2PRODUCTION_POST => array('onCode2productionPost', 0),
            Basic::EVENT_ROLLBACK_PRE => array('onRollbackPre', 0),
            Basic::EVENT_ROLLBACK_POST => array('onRollbackPost', 0),
            Basic::EVENT_CLEAN_PRE => array('onCleanPre', 0),
            Basic::EVENT_CLEAN_POST => array('onCleanPost', 0),
            Basic::EVENT_SYNCRONIZE_PRE => array('onSyncronizePre', 0),
            Basic::EVENT_SYNCRONIZE_POST => array('onSyncronizePost', 0),
            Basic::EVENT_CANCEL_DOWNLOAD_PRE => array('onCancelDownloadPre', 0),
            Basic::EVENT_CANCEL_DOWNLOAD_POST => array('onCancelDownloadPost', 0),
            Basic::EVENT_CANCEL_CODE2PRODUCTION_PRE => array('onCancelCode2productionPre', 0),
            Basic::EVENT_CANCEL_CODE2PRODUCTION_POST => array('onCancelCode2productionPost', 0),

            Basic::EVENT_EXECUTE => array('onExecute', 0),
        );
    }

    public function onInitializePre(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onInitializePost(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onDownloadPre(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onDownloadPost(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onDownloadAdaptCode(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onCode2productionPre(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onCode2productionPost(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onRollbackPre(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onRollbackPost(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onCleanPre(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onCleanPost(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onSyncronizePre(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onSyncronizePost(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onCancelDownloadPre(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onCancelDownloadPost(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onCancelCode2productionPre(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onCancelCode2productionPost(OperationEvent $event)
    {
        $event->getDeploy()->getLogger()->info(__METHOD__);
    }

    public function onExecute(ExecuteEvent $event)
    {
    }
}
