<?php
namespace Dubture\Nagios\Exception;
use Dubture\Nagios\Event\NagiosEvent;
use Dubture\Nagios\Event\NagiosErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionHandler implements EventSubscriberInterface
{
    public function onError(NagiosErrorEvent $event)
    {        
        $plugin = $event->getPlugin();
        $plugin['output']->critical($event->errstr);        
    }    
    
    public static function getSubscribedEvents()
    {
        return array(NagiosEvent::ERROR => array('onError', -255));
    }    
}