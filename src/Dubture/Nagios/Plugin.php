<?php
namespace Dubture\Nagios;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Plugin extends \Pimple
{

    protected $input;
    protected $output;

    public function __construct(array $definition = array(), $debug = true)
    {
        if ($debug === false){            
            error_reporting(0);
            set_error_handler(array($this, 'handleError'));            
        }
        
        $app = $this;
        $this->setupInput($definition);
        $this->output = new ConsoleOutput();

    }
        
    protected  function setupInput(array $definition)
    {

        $inputDefinition= new InputDefinition();
        
        foreach ($definition as $key => $val) {
            
            if (is_numeric($key)) {
                $inputDefinition->addArgument(new InputArgument($val));
            } else {                
                $inputDefinition->addOption(new InputOption(str_replace("--", "", $key)));
            }
        }
        
        $this->input = $inputDefinition;
        
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($this->output) {
            $this->fail('error handled ' . $errstr);
        }
        $this->shutdown();        
    }

    protected function fail($message)
    {
        $this->output->writeln($message);
    }

    public function run(\Closure $handler)
    {
        try {

            $reflection = new \ReflectionFunction($handler);
            $argv = new ArgvInput();
            $argv->bind($this->input);
            $argv->getArguments();
            
            $params = $reflection->getParameters();            
//             echo get_class($handler);
//             var_dump($argv->getArguments());
//             var_dump($argv->getFirstArgument());
//             $output = $handler();
//             $writer->writeln($output);

        } catch (Exception $e) {

        }
    }

    public function log($message)
    {

    }
    
    public function shutdown() {
        
        exit();
        
    }
}
