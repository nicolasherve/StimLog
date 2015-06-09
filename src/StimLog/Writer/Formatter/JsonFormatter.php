<?php

namespace StimLog\Writer\Formatter;

use StimLog\Event\LogEvent;

/**
 * Class used to format a log event for JSON
 *
 * TODO Test
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class JsonFormatter implements LogFormatter {

    public function formatEvent(LogEvent $event) {
        // Log entry
        $entry = array();

        // Log message
        $entry['message'] = '';
        if ($event->hasMessage()) {
            $entry['message'] = $event->getMessage();
        }

        // Permanent values
        $entry['date'] = $event->getDate()->toString();
        $trace = $event->getTrace();
        $entry['class'] = $trace['class'];
        $entry['line'] = $trace['line'];
        $entry['level'] = $event->getLevel()->toString();

        // Exception message, if any
        if ($event->hasException()) {
            $entry['exception'] = $this->_generateExceptionMessage($event->getException());
        }

        // Context message, if any
        if ($event->hasContext()) {
            $entry['context'] = $this->_generateContextMessage($event->getContext());
        }

        return json_encode($entry, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    // TODO Handle nested exception ($e->getPrevious)
    public function formatException(\Exception $e) {
        $entry = array();

        $entry['class'] = get_class($e);
        $entry['message'] = $e->getMessage();
        $entry['file'] = $e->getFile();
        $entry['line'] = $e->getLine();
        $entry['trace'] = $e->getTraceAsString();

        return $entry;
    }

    private function _generateExceptionMessage(\Exception $exception) {
        return $this->formatException($exception);
    }

    private function _generateContextMessage(array $context) {
        $entry = array();

        foreach ($context as $key => $value) {
            $entry[$key] = $this->_formatContextValue($value);
        }

        return $entry;
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
