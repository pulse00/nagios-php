<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <robert@dubture.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Nagios\Exception;
use Dubture\Nagios\Event\NagiosEvent;
use Dubture\Nagios\Event\NagiosErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExceptionHandler
 * @package Dubture\Nagios\Exception
 */
class ExceptionHandler implements EventSubscriberInterface
{
    /**
     * @param NagiosErrorEvent $event
     */
    public function onError(NagiosErrorEvent $event)
    {        
        $plugin = $event->getPlugin();
        $plugin['output']->critical($event->errstr);        
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(NagiosEvent::ERROR => array('onError', -255));
    }    
}