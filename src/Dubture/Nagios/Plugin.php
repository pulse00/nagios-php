<?php
namespace Dubture\Nagios;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Dubture\Nagios\Console\NagiosOutput;

class Plugin extends \Pimple
{
    /**
     * 
     * @var InputDefinition $input
     */
    protected $input;
    
    /**
     * @see http://nagios.sourceforge.net/docs/3_0/pluginapi.html
     */
    const OK = 0;
    const WARNING = 1;
    const CRITICAL = 2;
    const UNKNOWN = 3;

    public function __construct($debug = false)
    {
        if ($debug === false){            
            error_reporting(0);
            set_error_handler(array($this, 'handleError'));
        }
        
        $app = $this;        
        $app['process'] = function($c) {            
            return new Process($c['commandline']);
        };
        
        $this->input = new InputDefinition();;

    }
        
    public function handleError($errno, $errstr, $errfile, $errline)
    {        
        $output = new NagiosOutput();        
        $output->critical($errstr);
    }

    public function run(\Closure $handler)
    {
        try {
                        
            $params = $this->processArguments(new \ReflectionFunction($handler));
            $output = call_user_func_array($handler, $params);
            $nagios= new NagiosOutput();
            $nagios->report($output);
            
        } catch (\Exception $e) {
                        
            if ($e->getMessage() === 'Not enough arguments.') {
                $app = new Application();
                $app->renderException($e, new ConsoleOutput());
                exit(self::CRITICAL);
            }            
            
            $nagios= new NagiosOutput();
            $nagios->critial();
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