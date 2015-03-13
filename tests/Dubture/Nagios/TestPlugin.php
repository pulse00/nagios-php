<?php

namespace Dubture\Nagios;

use Symfony\Component\Console\Input\ArrayInput;

/**
 * Class TestPlugin
 * @package Dubture\Nagios
 */
class TestPlugin extends Plugin
{

    /**
     * @var array
     */
    private $arguments;

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments = array())
    {
        parent::__construct(true);
        $this->arguments = array_merge(array('plugin' => $this), $arguments);
    }

    /**
     * @return ArrayInput
     */
    protected function getInput()
    {
        return new ArrayInput($this->arguments);
    }

    /**
     * @return bool
     */
    public function doExitOnOutput()
    {
        return false;
    }
}