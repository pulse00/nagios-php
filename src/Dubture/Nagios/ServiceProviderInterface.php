<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <r.gruendler@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Nagios;

interface ServiceProviderInterface
{    
    function register(Plugin $plugin);
}