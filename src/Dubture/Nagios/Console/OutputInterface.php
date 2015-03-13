<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <robert@dubture.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Nagios\Console;

use Dubture\Nagios\Plugin;

/**
 * Interface OutputInterface
 * @package Dubture\Nagios\Console
 */
interface OutputInterface
{
    /**
     * @param Plugin $plugin
     * @return $this
     */
    function setPlugin(Plugin $plugin);

    /**
     * @param array $output
     * @return mixed
     */
    function report(array $output);

    /**
     * @param $message
     * @return mixed
     */
    function unknown($message);

    /**
     * @param $message
     * @return mixed
     */
    function warning($message);

    /**
     * @param $message
     * @return mixed
     */
    function critical($message);

    /**
     * @param $status
     * @return mixed
     */
    function doExit($status);   
}