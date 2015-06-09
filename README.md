StimLog - Logging framework for PHP
===================================

StimLog is a logging framework for PHP.

You can easily configure StimLog to change the active log level and thus change
the behavior of your loggers without modifying your code.

Its main characteristics are the following:

- Class based
- Trace the class and the line that triggered the log
- Easily configurable to:
 - change the active loggers of your application
 - change the active log level used in your application
- Easily extendable to add new components (`Writer`s and `Formatter`s)
- A [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) compliant logger is provided


Core concepts
=============

Every `Logger` instance is dedicated to a class, which makes StimLog a logging framework only for PHP classes.

A `Logger` has a list of `Writer`s (sometimes called _Handlers_ or _Appenders_) that will handle the log data.
Several `Writer`s are provided, but you are free to provide yours (they will have to extend the `LogWriter` abstract class).

Each `Writer` has one `Formatter`, a component used to process the messages and display it in a particular form.
Several `Formatter`s are provided, but you are free to provide yours (they will have to implement the `LogFormatter` interface).

Last but not least, a configuration is required to activate loggers of your code at a specific level, and with specific `Writer`s.
This configuration makes it possible to easily change the behavior of your loggers among your application, in a quick way.
This congifuration part is freely inspired from the well-known Java logging framework: [log4j](http://logging.apache.org/log4j/) 

Usage
=====

Step 1: Logging in a PHP class
------------------------------

StimLog loggers have to be created using the `Logger::create()` operation, and giving the client class name as an argument.

Then, the loggers provide operations to log information with different levels.

``` php
<?php

namespace Example;

use StimLog\Logger\Logger;

class Foo {

	public function bar() {
		
		// First, instantiate the logger and give the class name as argument
		$logger = Logger::create(__CLASS__);
		
		// Log data
		$logger->error("This is an error");
		$logger->notice("This is a notice");		
	}
}
```

Step 2: Configuration
---------------------

StimLog relies on configuration settings that have to be set.

This settings are defined with a plain PHP `array`.

Each entry of this array will define one or multiple loggers'configuration (another `array`), through 3 settings:

- the namespace identifying the loggers
- the active level to use for the identified loggers
- the `Writer`s to use for the identified loggers

See the example below (used in our example as `stimlog.conf.php`):

``` php
<?php

// Loggers configuration
$loggers = array(

	// First loggers'configuration
	array (
        // This configuration will concern every logger under the Example namespace
        'class'=>'Example',
        
        // This configuration will trigger all loggers with debug level or higher
        'level'=>'debug',
        
        // The list of writers for this configuration
        'writers'=>array('StimLog\Writer\FileWriter')
    ),

);

```

Finally, you have to indicate StimLog the configuration file you want to use.

The `LoggerManager::setup()` operation is provided to do that.

``` php
<?php

use StimLog\Manager\LoggerManager;

LoggerManager::setup('/path/to/stimlog.conf.php');
```


Advanced Usage
==============

Log levels
----------
StimLog supports the log levels described by [RFC 5424](http://tools.ietf.org/html/rfc5424).

- **DEBUG** (100)
- **INFO** (200)
- **NOTICE** (250)
- **WARNING** (300)
- **ERROR** (400)
- **CRITICAL** (500)
- **ALERT** (550)
- **EMERGENCY** (600)


Detecting the log level
-----------------------
StimLog loggers provide operations to detect the active log level.

Thus, you can use methods such as `isDebugEnabled()` (for the `DEBUG` level) to test if the concerned level is active.

``` php
if ($logger->isDebugEnabled()) {
	// Perform debug operations...
	
}
```

Log operations
--------------

StimLog default logger provides different ways of logging information.

Basically, it is possible to log 3 types of data:

- a `string`, representing the message to log
- an `Exception`
- an associative `array`, containing some extra values for the log (called here _context_)

### Logging with 1 argument

You can use the logging operations to log one parameter:

- a message (as a `string`)

``` php
// Log a message
$logger->notice("Just a notice");
```

or

- an `Exception`

``` php
try {
	throw new \Exception("An exception occurred!!!");
}
catch (\Exception $e) {
	// Log an exception
	$logger->error($e);
}
```

### Logging with 2 arguments

You can use the logging operations to log two parameters:

- a message (as a `string`)
- an `Exception`

``` php
try {
	throw new \Exception("An exception occurred!!!");
}
catch (\Exception $e) {
	// Log an exception
	$logger->error("An exception occurred as expected", $e);
}
```

or

- a message (as a `string`)
- a list of context values (as an associative `array`)

``` php
// Log a message
$logger->notice("Just a notice", array('user'=>$user, 'booleanValue'=>false));
```

or

- an `Exception`
- a list of context values (as an associative `array`)

``` php
try {
	throw new \Exception("An exception occurred!!!");
}
catch (\Exception $e) {
	// Log an exception
	$logger->error($e, array('user'=>$user, 'booleanValue'=>false));
}
```

### Logging with 3 arguments

You can use the logging operations to log three parameters:

- a message (as a `string`)
- an `Exception`
- a list of context values (as an associative `array`)

``` php
try {
	throw new \Exception("An exception occurred!!!");
}
catch (\Exception $e) {
	// Log an exception
	$logger->error("An exception occurred as expected", $e, array('user'=>$user, 'booleanValue'=>false));
}
```

PSR-3 compliant logger
----------------------
Alternatively to the default StimLog logger, a [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) compliant
logger is provided.

It is the `StimLog\Logger\Psr3Logger`.


About
=====

Requirements
------------

StimLog works with PHP 5.3 or above.

Author
------

Nicolas Hervé - <nherve@gmail.com>

License
-------

StimLog is licensed under the MIT License - see the `LICENSE` file for details.