<?php
require_once __DIR__ . '/autoload.php';
$plugin = new Dubture\Nagios\Plugin();

$plugin->run(function($name, $foo = 'bar') use($plugin) {
    
    return $name;
    
});