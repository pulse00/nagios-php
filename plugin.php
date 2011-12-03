<?php

/**
 * Example load-average plugin with a single
 * argument for the load threshold.
 *  
 */

require_once __DIR__ . '/nagios.phar';
use Dubture\Nagios\Plugin;

$plugin = new Plugin();
$plugin->run(function($threshold) use ($plugin) {
                
    $plugin['commandline'] = 'uptime';
    $process = $plugin['process'];
    
    if ($process->run() > 0) { 
        return array(Plugin::CRITICAL,'Error running uptime command');        
    }
    
    $output = $process->getOutput();
    $loads = explode(' ', substr($output, strpos($output, "load averages:")+15));
    
    if ($loads[1] > $threshold) {
        return array(Plugin::WARNING,"Load average: " . $loads[1]);
    }
        
    return array(Plugin::OK,"Load average: " . $loads[1]);
    
});