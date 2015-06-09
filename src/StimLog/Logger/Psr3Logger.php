<?php

namespace StimLog\Logger;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use StimLog\Event\LogEvent;
use StimLog\Level\LogLevel;
use StimLog\Manager\LoggerManager;

/**
 * Logger made to be PSR-3 compliant
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class Psr3Logger extends AbstractLogger implements LoggerInterface {

    /**
     * Create and return a Logger
     *
     * @param string $className the class name which is observed by the logger
     * @return Psr3Logger
     */
    public static function create($className) {
        // The best logger configuration for the given class name
        $bestLoggerConfiguration = LoggerManager::findBestLoggerConfigurationForClass($className);

        // If no logger configuration was found for the given class name
        if (!isset($bestLoggerConfiguration)) {
            // Return an empty logger
            return new NullLogger($className);
        }

        // A logger configuration has been found for the given class name
        $logger = new Psr3Logger($className);
        $logger->_targetClassName = $bestLoggerConfiguration['class'];
        $logger->_level = LogLevel::createFromCode($bestLoggerConfiguration['level']);
        $logger->_setupWriters($bestLoggerConfiguration['writers']);

        return $logger;
    }

    /**
     * System is unusable
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array()) {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array()) {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array()) {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array()) {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array()) {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array()) {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array()) {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array()) {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array()) {
        // If the given level is not valid
        if (!LogLevel::isValid($level)) {
            throw new InvalidArgumentException("The given logger level [" . $level . "] is not valid");
        }

        // If the given level is not handled by the logger
        if (!$this->_isEnabledForLevel($level)) {
            return;
        }

        // Create a log event
        $event = LogEvent::create($this->_className, $level);

        // If there is a given context
        if (!empty($context)) {
            $message = $this->_interpolate($message, $context);
        }

        // Set the message
        $event->setMessage($message);

        // Set the exception, if any
        if (isset($context['exception'])) {
            $event->setException($context['exception']);
        }

        // Dispatch the log event to the writers
        $this->_dispatchEventToWriters($event);
    }

    /**
     * Interpolates context values into the message placeholders.
     * 
     * @param string $message
     * @param array $context
     * @return string
     */
    private function _interpolate($message, array $context) {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

}
