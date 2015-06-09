<?php

namespace StimLog\Logger;

use StimLog\Event\LogEvent;
use StimLog\Level\LogLevel;
use StimLog\Writer\LogWriter;

/**
 * Abstract class for StimLogger
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
abstract class AbstractLogger {

    /**
     * The class name of the component using the logger
     * 
     * @var string
     */
    protected $_className;

    /**
     * The target class name or namespace of the logger configuration
     * 
     * @var string
     */
    protected $_targetClassName;

    /**
     * List of writers for the logger
     *
     * @var array
     */
    protected $_writers;

    /**
     * Level applied for the logger
     * 
     * The level is PSR-3 compliant
     * 
     * @var LogLevel
     */
    protected $_level;

    /**
     * Protected constructor
     */
    protected function __construct($className) {
        $this->_className = $className;
        $this->_targetClassName = null;
        $this->_writers = array();
        $this->_level = null;
    }

    public function getClassName() {
        return $this->_className;
    }

    public function getTargetClassName() {
        return $this->_targetClassName;
    }

    public function getLevel() {
        return $this->_level;
    }

    protected function _setupWriters(array $writers) {
        $this->_writers = array();
        foreach ($writers as $writerClassName) {
            $writer = new $writerClassName();
            if (!$writer instanceof LogWriter) {
                $actualType = gettype($writer);
                throw new LoggerException("The object given as log writer is of type [$actualType], [LogWriter] expected");
            }
            $this->_writers[] = $writer;
        }
    }

    protected function _dispatchEventToWriters(LogEvent $event) {
        foreach ($this->_writers as $writer) {
            $writer->processEvent($event);
        }
    }

    protected function _isEnabledForLevel($level) {
        if (!isset($this->_level)) {
            return false;
        }
        return $this->_level->toInt() <= $level;
    }

    public function isDebugEnabled() {
        return $this->_isEnabledForLevel(LogLevel::DEBUG);
    }

    public function isInfoEnabled() {
        return $this->_isEnabledForLevel(LogLevel::INFO);
    }

    public function isNoticeEnabled() {
        return $this->_isEnabledForLevel(LogLevel::NOTICE);
    }

    public function isWarningEnabled() {
        return $this->_isEnabledForLevel(LogLevel::WARNING);
    }

    public function isErrorEnabled() {
        return $this->_isEnabledForLevel(LogLevel::ERROR);
    }

    public function isCriticalEnabled() {
        return $this->_isEnabledForLevel(LogLevel::CRITICAL);
    }

    public function isAlertEnabled() {
        return $this->_isEnabledForLevel(LogLevel::ALERT);
    }

    public function isEmergencyEnabled() {
        return $this->_isEnabledForLevel(LogLevel::EMERGENCY);
    }

}
