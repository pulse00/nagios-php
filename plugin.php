<?php
require_once __DIR__ . '/autoload.php';
use Dubture\Nagios\Plugin;

$plugin = new Plugin();
$plugin->run(function($name) use ($plugin) {
                
    return array(Plugin::OK, array('hello' => $name, 'test'));
    
});