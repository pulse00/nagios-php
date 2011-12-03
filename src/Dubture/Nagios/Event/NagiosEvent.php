<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <r.gruendler@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Nagios\Event;
use Dubture\Nagios\Plugin;
use Symfony\Component\EventDispatcher\Event;

class NagiosEvent extends Event
{    
    const BEFORE_OK         = "nagios.before_ok";
    const BEFORE_WARNING    = "nagios.before_warning";
    const BEFORE_CRITICAL   = "nagios.before_critical";
    const BEFORE_UNKNOWN    = "nagios.before_unknown";
    const ERROR             = "nagios.error";
    
    protected $plugin;
    
    public function __construct(Plugin $plugin)
    {        
        $this->plugin = $plugin;        
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}