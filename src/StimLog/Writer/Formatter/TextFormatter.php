<?php

namespace StimLog\Writer\Formatter;

use Exception;
use StimLog\Event\LogEvent;

/**
 * Class used to format a log in a simple, single line of text
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class TextFormatter implements LogFormatter {

    public function formatEvent(LogEvent $event) {

        // Log message
        $message = '';

        // Add the given message, if any
        if ($event->hasMessage()) {
            $message = $event->getMessage();
        }

        // Add the given exception, if any
        if ($event->hasException()) {
            $message .= PHP_EOL . $this->_generateExceptionMessage($event->getException());
        }

        // Add the given context, if any
        if ($event->hasContext()) {
            $message .= PHP_EOL . $this->_generateContextMessage($event->getContext());
        }

        // Permanent values
        $date = $event->getDate()->toString();
        $trace = $event->getTrace();
        $level = $event->getLevel()->toString();

        return sprintf("%s %-10s %s.%s %s" . PHP_EOL, $date, '[' . strtoupper($level) . ']', $trace['class'], $trace['line'], $message);
    }

    public function formatException(Exception $e) {
        $message = 'Exception ' . get_class($e) . ' with message "' . $e->getMessage() . '" in ' . $e->getFile() . '(' . $e->getLine() . ')' . PHP_EOL . $e->getTraceAsString();
        while ($e = $e->getPrevious()) {
            $message .= PHP_EOL . PHP_EOL . 'Caused by Exception ' . get_class($e) . ' with message "' . $e->getMessage() . '" in ' . $e->getFile() . '(' . $e->getLine() . ')' . PHP_EOL . $e->getTraceAsString();
        }

        return $message;
    }

    private function _generateExceptionMessage(Exception $exception) {
        return $this->formatException($exception);
    }

    private function _generateContextMessage(array $context) {
        $message = '';
        foreach ($context as $key => $value) {
            $message .= '[' . $key . '] => ' . $this->_formatContextValue($value);
            $message .= PHP_EOL;
        }

        return $message;
    }

    private function _formatContextValue($value) {
        if (is_object($value)) {
            return var_export($value, true);
        }
        if (is_array($value)) {
            return print_r($value, true);
        }
        return (string) $value;
    }

}
