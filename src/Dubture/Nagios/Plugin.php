<?php
namespace Dubture\Nagios;
use Dubture\Nagios\Event\NagiosEvent;
use Dubture\Nagios\Console\NagiosOutput;
use Dubture\Nagios\Event\NagiosErrorEvent;
use Dubture\Nagios\Exception\ExceptionHandler;
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

class Plugin extends \Pimple
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

    public function __construct($debug = true)
    {
        if ($debug === false){            
            error_reporting(0);            
        }
        
        set_error_handler(array($this, 'handleError'));
        
        $app = $this;
        
        $this['autoloader'] = $this->share(function() {
            $loader = new UniversalClassLoader();
            $loader->register();        
            return $loader;
        });        
        
        $this['dispatcher'] = $this->share(function() use ($app) {            
            $dispatcher = new EventDispatcher();
            return $dispatcher;
        });

        $this['exception_handler'] = $this->share(function() use ($app) {
            $handler = new ExceptionHandler();
            $app['dispatcher']->addSubscriber($handler);
            return $handler;
        });
        
        $this['process'] = function($c) {
            return new Process($c['commandline']);
        };
        
        $this['output'] = $this->share(function() use($app) {
            $output = new NagiosOutput();
            $output->setPlugin($app);
            return $output;
        });
        
        $this->input = new InputDefinition();
        $handler = $this['exception_handler'];

    }
    
    public function register(ServiceProviderInterface $provider, array $values = array()) 
    {        
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
                
        $provider->register($this);
        
    }
        
    public function handleError($errno, $errstr, $errfile, $errline)
    { 
        $event = new NagiosErrorEvent($this, $errno, $errstr, $errfile, $errline);                
        $this['dispatcher']->dispatch(NagiosEvent::ERROR, $event);
    }

    public function run(\Closure $handler)
    {
        try {
                        
            $params = $this->processArguments(new \ReflectionFunction($handler));
            $output = call_user_func_array($handler, $params);
            $this['output']->report($output);
            
        } catch (\Exception $e) {
                        
            if ($e->getMessage() === 'Not enough arguments.') {
                $app = new Application();
                $app->renderException($e, new ConsoleOutput());
                exit(self::CRITICAL);
            }
            $this['output']->critial();
        }
    }
    
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
        
        $argv = new ArgvInput();
        $argv->bind($this->input);
        $argv->validate();

        foreach ($preprocess as $param) {            
            if (isset($param['option'])) {
                $params[] = $argv->getOption($param['option']);
            } else {
                $params[] = $argv->getArgument($param['arg']);
            }
        }
        
        return $params;
        
    }
}