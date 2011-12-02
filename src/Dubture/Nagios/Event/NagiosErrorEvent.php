<?php
namespace Dubture\Nagios\Event;
use Dubture\Nagios\Plugin;
use Dubture\Nagios\Event\NagiosEvent;

class NagiosErrorEvent extends NagiosEvent
{    
    public $errno;
    public $errstr;
    public $errfile;
    public $errline;
    
    public function __construct(Plugin $plugin, $errno, $errstr, $errfile, $errline) 
    {        
        parent::__construct($plugin);
        $this->errno = $errno;
        $this->errstr = $errstr;
        $this->errfile = $errfile;
        $this->errline = $errline;                
    }
}