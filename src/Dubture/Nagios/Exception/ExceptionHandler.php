<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <r.gruendler@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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