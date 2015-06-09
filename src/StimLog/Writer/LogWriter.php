<?php

namespace StimLog\Writer;

use StimLog\Event\LogEvent;
use StimLog\Writer\Formatter\LogFormatter;

/**
 * Abstract class for log writers
 * 
 * A log writer has one formatter
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
abstract class LogWriter {

    protected $_formatter;

    public function getFormatter() {
        return $this->_formatter;
    }

    public function setFormatter(LogFormatter $formatter) {
        $this->_formatter = $formatter;
    }

    public abstract function processEvent(LogEvent $event);
}
