<?php
namespace Dubture\Nagios\Console;

use Dubture\Nagios\Plugin;

interface OutputInterface
{    
    function setPlugin(Plugin $plugin);
    
    function report(array $output);

    function unknown($message);
    
    function warning($message);
    
    function critical($message);
    
    function doExit($status);   
}