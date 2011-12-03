nagios-php
==========

Simple utility to help writing nagios plugins in PHP inspired
by the [Silex microframework](https://github.com/fabpot/Silex).

Usage:
======

Example - check_hello :

```php
<?php 

require_once __DIR__ . '/nagios.phar';

use Dubture\Nagios\Plugin;

$plugin = new Plugin();
$plugin->run(function($name, $foo = 'bar') use ($plugin) {
                
    return array(Plugin::OK, array('hello' => $name, $foo));
    
});

```

Running the above plugin using `check_hello pulse00` will result in an
nagios service state `OK` and the multiline output:

```
hello | pulse00
bar
```

The ` Dubture\Nagios\Plugin::run()` method expects a `Closure` whose
method signature determines the nagios plugin arguments. A parameter
without a default value represents a mandatory argument, a parameter with
a default value represents an optional argument.

The plugin in the above example has one mandatory argument `name` and
an optional argument `foo` with the default value `bar`.

The `Closure` should return an array with the status code as the first
element and the output as the second argument, which will be formatted
after the following rules:

- If the second array element is a string, the output is a single-line message
- If the second array element is an array, the output is a multi-line message.
- Every element of the multiline message can either be a simple message (literal value),
or a message / performance output if the array paramater is a key/value pair.

Installation
============

Download and include the [nagios.phar](https://github.com/pulse00/nagios-php/raw/master/nagios.phar) file. That's all.