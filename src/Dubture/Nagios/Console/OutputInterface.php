<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <r.gruendler@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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