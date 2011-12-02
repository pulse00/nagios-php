<?php
namespace Dubture\Nagios;

interface ServiceProviderInterface
{    
    function register(Plugin $plugin);
}