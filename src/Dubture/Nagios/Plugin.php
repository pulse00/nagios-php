<?php

/*
 * This file is part of the nagios-php utility.
 *
 * (c) Robert Gruendler <robert@dubture.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dubture\Nagios;

use Dubture\Nagios\Event\NagiosEvent;
use Dubture\Nagios\Console\NagiosOutput;
use Dubture\Nagios\Event\NagiosErrorEvent;
use Dubture\Nagios\Exception\ExceptionHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Plugin
 * @package Dubture\Nagios
 */
class Plugin extends Container
{
    /** 
     * @var InputDefinition $input
     */
    protected $input;
    
    /** 
     * @var NagiosOutput $output
     */
    protected $output;
    
    /**
     * @see http://nagios.sourceforge.net/docs/3_0/pluginapi.html
     */
    const OK = 0;
    const WARNING = 1;
    const CRITICAL = 2;
    const UNKNOWN = 3;

    /**
     * @param bool $debug
     */
    public function __construct($debug = true)
    {
        if ($debug === false){            
            error_reporting(0);            
        }
        
        set_error_handler(array($this, 'handleError'));
        
        $app = $this;

        $this['autoloader'] = function() {
            $loader = new UniversalClassLoader();
            $loader->register();        
            return $loader;
        };
        
        $this['dispatcher'] = function() use ($app) {
            $dispatcher = new EventDispatcher();
            return $dispatcher;
        };

        $this['exception_handler'] = function() use ($app) {
            $handler = new ExceptionHandler();
            $app['dispatcher']->addSubscriber($handler);
            return $handler;
        };
        
        $this['process'] = function($c) {
            return new Process($c['commandline']);
        };
        
        $this['output'] = function() use($app) {
            $output = new NagiosOutput();
            $output->setPlugin($app);
            return $output;
        };
        
        $this->input = new InputDefinition();
        $handler = $this['exception_handler'];

    }

    /**
     * @param ServiceProviderInterface $provider
     * @param array $values
     * @return void|static
     */
    public function register(ServiceProviderInterface $provider, array $values = array()) 
    {        
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
                
        $provider->register($this);
        
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    { 
        $event = new NagiosErrorEvent($this, $errno, $errstr, $errfile, $errline);                
        $this['dispatcher']->dispatch(NagiosEvent::ERROR, $event);
    }

    /**
     * @param callable $handler
     */
    public function run(\Closure $handler)
    {
        try {
            // get the arguments from the commandline
            $params = $this->processArguments(new \ReflectionFunction($handler));
            // now call the handler with those arguments
            $output = call_user_func_array($handler, $params);
            // report the output
            $this['output']->report($output);
        } catch (\Exception $e) {
                        
            if ($e->getMessage() === 'Not enough arguments.') {
                $app = new Application();
                $app->renderException($e, new ConsoleOutput());
                $this['output']->critical();
            }
            $this['output']->critical($e->getMessage());
        }
    }

    /**
     * @param \ReflectionFunction $reflector
     * @return array
     */
    protected function processArguments(\ReflectionFunction $reflector)
    {
        $params = array();
        $preprocess = array();
        
        foreach ($reflector->getParameters() as $param) {        
            $name = $param->getName();        
            if ($param->isDefaultValueAvailable()) {                
                $default = $param->getDefaultValue();                
                $this->input->addOption(new InputOption($name, null, InputOption::VALUE_OPTIONAL, '', $default));
                $preprocess[] = array('option' => $name);
                
            } else {
                $this->input->addArgument(new InputArgument($name, InputArgument::REQUIRED));
                $preprocess[] = array('arg' => $name);
            }
        }
        
        $input = $this->getInput();
        $input->bind($this->input);
        $input->validate();

        foreach ($preprocess as $param) {            
            if (isset($param['option'])) {
                $params[] = $input->getOption($param['option']);
            } else {
                $params[] = $input->getArgument($param['arg']);
            }
        }
        
        return $params;
    }

    /**
     * @return InputInterface
     */
    protected function getInput()
    {
        return new ArgvInput();
    }

    /**
     * @return bool
     */
    public function doExitOnOutput()
    {
        return true;
    }
}