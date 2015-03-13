<?php

namespace Dubture\Nagios;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Dubture\Nagios\Event\NagiosEvent;

/**
 * Class PluginTest
 * @package Dubture\Nagios
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    public function testPluginExecution()
    {
        $plugin = new TestPlugin(array('foo' => 'bar'));

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $plugin['dispatcher'];

        $calledEvents = array();

        $dispatcher->addListener(NagiosEvent::BEFORE_OK, function(NagiosEvent $event, $type) use(&$calledEvents) {
            $calledEvents[$type] = true;
        });

        $output = array();

        $plugin->run(function(Plugin $plugin, $foo) use(&$output) {
            $output['string'] = "Test plugin " . $foo;

            return array(Plugin::OK, $output['string']);
        });

        $this->assertTrue($calledEvents['nagios.before_ok']);
        $this->assertEquals('Test plugin bar', $output['string']);
    }
}