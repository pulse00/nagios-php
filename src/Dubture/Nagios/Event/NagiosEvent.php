<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <robert@dubture.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Nagios\Event;
use Dubture\Nagios\Plugin;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NagiosEvent
 * @package Dubture\Nagios\Event
 */
class NagiosEvent extends Event
{    
    const BEFORE_OK         = "nagios.before_ok";
    const BEFORE_WARNING    = "nagios.before_warning";
    const BEFORE_CRITICAL   = "nagios.before_critical";
    const BEFORE_UNKNOWN    = "nagios.before_unknown";
    const ERROR             = "nagios.error";

    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {        
        $this->plugin = $plugin;        
    }

    /**
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }
}