<?php
namespace Dubture\Nagios\Console;
use Dubture\Nagios\Event\NagiosEvent;
use Dubture\Nagios\Plugin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class NagiosOutput extends ConsoleOutput implements OutputInterface
{   
    protected $dispatcher;
    protected $plugin; 
    
    public function setPlugin(Plugin $plugin) 
    {        
        $this->dispatcher = $plugin['dispatcher'];
        $this->plugin = $plugin;        
    }
    
    public function report(array $output) 
    {        
        if (!is_array($output) || count($output) == 0) {
            $this->unknown();
        }
        
        $status = $output[0];
        
        if (isset($output[1]) && (strlen($message = $output[1]))) {
            $this->writeln($output[1]);
        }
                
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
                
        exit($status);
    }
}