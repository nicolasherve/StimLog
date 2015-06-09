<?php

namespace StimLog\Logger;

use Psr\Log\InvalidArgumentException;
use StimLog\Event\LogEvent;
use StimLog\Level\LogLevel;
use StimLog\Manager\LoggerManager;

/**
 * Default logger for StimLog
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class Logger extends AbstractLogger {

    /**
     * Create and return a Logger
     *
     * @param string $className the class name which is observed by the logger
     * @return Logger
     */
    public static function create($className) {
        // The best logger configuration for the given class name
        $bestLoggerConfiguration = LoggerManager::findBestLoggerConfigurationForClass($className);

        // If no logger configuration was found for the given class name
        if (!isset($bestLoggerConfiguration)) {
            // Return an empty logger
            return new Logger($className);
        }

        // A logger configuration has been found for the given class name
        $logger = new Logger($className);
        $logger->_targetClassName = $bestLoggerConfiguration['class'];
        $logger->_level = LogLevel::createFromCode($bestLoggerConfiguration['level']);
        $logger->_setupWriters($bestLoggerConfiguration['writers']);

        return $logger;
    }

    /**
     * System is unusable
     *
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function emergency($messageOrException, $contextOrException = null, array $context = array()) {
        $this->log(LogLevel::EMERGENCY, $messageOrException, $contextOrException, $context);
    }

    /**
     * Action must be taken immediately
     *
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function alert($messageOrException, $contextOrException = null, array $context = array()) {
        $this->log(LogLevel::ALERT, $messageOrException, $contextOrException, $context);
    }

    /**
     * Critical conditions
     *
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function critical($messageOrException, $contextOrException = null, array $context = array()) {
        $this->log(LogLevel::CRITICAL, $messageOrException, $contextOrException, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored
     *
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function error($messageOrException, $contextOrException = null, array $context = array()) {
        $this->log(LogLevel::ERROR, $messageOrException, $contextOrException, $context);
    }

    /**
     * Exceptional occurrences that are not errors
     *
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function warning($messageOrException, $contextOrException = null, array $context = array()) {
        $this->log(LogLevel::WARNING, $messageOrException, $contextOrException, $context);
    }

    /**
     * Normal but significant events
     *
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function notice($messageOrException, $contextOrException = null, array $context = array()) {
        $this->log(LogLevel::NOTICE, $messageOrException, $contextOrException, $context);
    }

    /**
     * Interesting events
     *
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function info($messageOrException, $contextOrException = null, array $context = array()) {
        $this->log(LogLevel::INFO, $messageOrException, $contextOrException, $context);
    }

    /**
     * Detailed debug information
     *
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function debug($messageOrException, $contextOrException = null, array $context = array()) {
        $this->log(LogLevel::DEBUG, $messageOrException, $contextOrException, $context);
    }

    /**
     * Logs with an arbitrary level
     * 
     * 1) With 1 argument
     * 
     * $logger->log($exception)
     * $logger->log($message)
     * 
     * 2) With 2 arguments
     * 
     * $logger->log($message, $exception)
     * $logger->log($message, $context)
     * $logger->log($exception, $context)
     * 
     * 3) With 3 arguments
     * 
     * $logger->log($message, $exception, $context)
     * 
     * @param integer $level
     * @param string|Exception $messageOrException
     * @param array|Exception $contextOrException
     * @param array $context
     */
    public function log($level, $messageOrException, $contextOrException = null, array $context = array()) {
        // If the given level is not valid
        if (!LogLevel::isValid($level)) {
            throw new InvalidArgumentException("The given logger level [" . $level . "] is not valid");
        }

        // If the given level is not handled by the logger
        if (!$this->_isEnabledForLevel($level)) {
            return null;
        }

        // Create a log event
        $event = LogEvent::create($this->_className, $level);

        // Handle the 1st argument
        $this->_handleFirstArgument($event, $messageOrException);

        // Handle the 2nd argument
        if (isset($contextOrException)) {
            $this->_handleSecondArgument($event, $contextOrException);
        }

        // Handle the 3rd argument
        if (!empty($context)) {
            $event->setContext($context);
        }

        // Dispatch the log event to the writers
        $this->_dispatchEventToWriters($event);
    }

    private function _handleFirstArgument(LogEvent $event, $messageOrException) {
        if (is_string($messageOrException)) {
            $event->setMessage($messageOrException);
            return;
        }
        if ($messageOrException instanceof \Exception) {
            $event->setException($messageOrException);
            return;
        }
        throw new InvalidArgumentException("The 1st argument must be a string or an Exception");
    }

    private function _handleSecondArgument(LogEvent $event, $contextOrException) {
        if (is_array($contextOrException)) {
            $event->setContext($contextOrException);
            return;
        }
        if ($contextOrException instanceof \Exception) {
            $event->setException($contextOrException);
            return;
        }
        throw new InvalidArgumentException("The 2nd argument must be an array or an Exception");
    }

}
