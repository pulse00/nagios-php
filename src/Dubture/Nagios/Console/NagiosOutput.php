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

use Dubture\Nagios\Event\NagiosEvent;
use Dubture\Nagios\Plugin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Formats the plugin output according to the nagios specification.
 *
 * Class NagiosOutput
 * @package Dubture\Nagios\Console
 */
class NagiosOutput extends ConsoleOutput implements OutputInterface
{

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @param Plugin $plugin
     * @return $this
     */
    public function setPlugin(Plugin $plugin)
    {
        $this->dispatcher = $plugin['dispatcher'];
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * @param array $output
     * @return mixed|void
     */
    public function report(array $output)
    {
        if (!is_array($output) || count($output) == 0) {
            $this->unknown();
        }

        $status = $output[0];

        if (isset($output[1])) {
            $message = $output[1];
            if ((is_string($message))) {
                $this->writeln($output[1]);
            } else {
                if (is_array($message)) {
                    foreach ($message as $key => $line) {
                        if (is_numeric($key)) {
                            $this->writeln($line);
                        } else {
                            $this->writeln(sprintf("%s | %s", $key, $line));
                        }
                    }
                }
            }
        }

        $this->doExit($status);

    }

    /**
     * @param string $message
     * @return mixed|void
     */
    public function unknown($message = null)
    {
        if (null !== $message) {
            $this->write($message);
        }

        $this->doExit(Plugin::UNKNOWN);
    }

    /**
     * @param string $message
     * @return mixed|void
     */
    public function warning($message = null)
    {
        if (null !== $message) {
            $this->write($message);
        }

        $this->doExit(Plugin::WARNING);
    }

    /**
     * @param string $message
     * @return mixed|void
     */
    public function critical($message = null)
    {
        if (null !== $message) {
            $this->write($message);
        }

        $this->doExit(Plugin::CRITICAL);
    }

    /**
     * @param $status
     * @return mixed|void
     */
    public function doExit($status)
    {
        $type = null;

        switch ($status) {
            case Plugin::OK:
                $type = NagiosEvent::BEFORE_OK;
                break;
            case Plugin::WARNING:
                $type = NagiosEvent::BEFORE_WARNING;
                break;
            case Plugin::CRITICAL:
                $type = NagiosEvent::BEFORE_CRITICAL;
                break;
            case Plugin::UNKNOWN:
                $type = NagiosEvent::BEFORE_UNKNOWN;
                break;
        }

        if (null !== $type) {
            $this->dispatcher->dispatch($type, new NagiosEvent($this->plugin));
        }

        if ($this->plugin->doExitOnOutput()) {
            exit($status);
        }
    }
}