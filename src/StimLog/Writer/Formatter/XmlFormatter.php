<?php

namespace StimLog\Writer\Formatter;

use StimLog\Event\LogEvent;

/**
 * Class used to format a log eventfor XML
 * 
 * TODO Test
 * 
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class XmlFormatter implements LogFormatter {

    public function formatEvent(LogEvent $event) {
        // Exception message
        $exceptionMessage = '';
        if ($event->hasException()) {
            $exceptionMessage = $this->formatException($event->getException());
        }

        // Log message
        $message = '';

        // Add the given message, if any
        if ($event->hasMessage()) {
            $message = $event->getMessage();
        }

        // Permanent values
        $date = $event->getDate()->toString();
        $trace = $event->getTrace();
        $level = $event->getLevel()->toString();

        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startElement('entry');
        $xml->writeElement('date', $date);
        $xml->writeElement('message', htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
        $xml->writeElement('level', $level);
        $xml->writeElement('class', htmlspecialchars($trace['class'], ENT_QUOTES, 'UTF-8'));
        $xml->writeElement('line', $trace['line']);

        // Add the given exception, if any
        if ($event->hasException()) {
            $xml->writeElement('exception', $this->_generateExceptionMessage($event->getException()));
        }

        // Add the given context, if any
        if ($event->hasContext()) {
            $xml->writeElement('context', $this->_generateContextMessage($event->getContext()));
        }

        $xml->endElement();

        return $xml->outputMemory();
    }

    // TODO Handle nested exception ($e->getPrevious)
    public function formatException(\Exception $e) {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->writeElement('class', get_class($e));
        $xml->writeElement('message', htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        $xml->writeElement('file', htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8'));
        $xml->writeElement('line', $e->getLine());
        $xml->writeElement('trace', htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8'));

        return $xml->outputMemory();
    }

    private function _generateExceptionMessage(\Exception $exception) {
        return $this->formatException($exception);
    }

    private function _generateContextMessage(array $context) {
        $xml = new XMLWriter();
        $xml->openMemory();

        foreach ($context as $key => $value) {
            $xml->writeElement($key, htmlspecialchars($this->_formatContextValue($value), ENT_QUOTES, 'UTF-8'));
        }

        return $xml->outputMemory();
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
