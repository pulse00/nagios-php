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
use Dubture\Nagios\Event\NagiosEvent;

/**
 * Class NagiosErrorEvent
 * @package Dubture\Nagios\Event
 */
class NagiosErrorEvent extends NagiosEvent
{    
    public $errno;
    public $errstr;
    public $errfile;
    public $errline;

    /**
     * @param Plugin $plugin
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public function __construct(Plugin $plugin, $errno, $errstr, $errfile, $errline) 
    {        
        parent::__construct($plugin);
        $this->errno = $errno;
        $this->errstr = $errstr;
        $this->errfile = $errfile;
        $this->errline = $errline;                
    }
}