<?php
namespace Dubture\Nagios\Console;
use Dubture\Nagios\Plugin;
use Symfony\Component\Console\Output\ConsoleOutput;

class NagiosOutput extends ConsoleOutput
{    
    public function report(array $output) 
    {        
        if (is_array($output) || count($output) == 0) {
            $this->unknown();
        }
        
        $status = $output[0];
        $this->writeln($response);        
        $this->doExit($status);
        
    }
    
    public function unknown($message = null)
    {
        if (null !== $message) {
            $this->write($message);
        }
        
        $this->doExit(Plugin::UNKNOWN);
    }
    
    public function warning($message = null)
    {
        if (null !== $message) {
            $this->write($message);
        }
        
        $this->doExit(Plugin::WARNING);
    }    
    
    public function critical($message = null)
    {        
        if (null !== $message) {
            $this->write($message);
        }        
        
        $this->doExit(Plugin::CRITICAL);                
    }
    
    protected function doExit($status)
    {
        exit($status);
    }
}