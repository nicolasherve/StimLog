<?php

namespace StimLog\Event;

use StimLog\Date\DateMilli;
use StimLog\Level\LogLevel;

/**
 * Class used to represent a log event
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class LogEvent {

    /**
     * Date of the event
     *
     * @var DateMilli
     */
    private $_date;

    /**
     * Log level of the event
     *
     * @var LogLevel
     */
    private $_level;

    /**
     * Message of the event
     *
     * @var string
     */
    private $_message;

    /**
     * Eventual exception related to the event
     *
     * @var Exception
     */
    private $_exception;

    /**
     * Backtrace of the event
     *
     * @var array
     */
    private $_trace;

    /**
     * Context of the event
     *
     * @var array
     */
    private $_context;

    /**
     * Private constructor
     */
    private function __construct() {
        $this->_trace = array();
        $this->_context = array();
    }

    /**
     * Create a new log event and return it
     * 
     * @param string $className the class name where the event was created
     * @param integer $level the log level of the event
     * @return LogEvent
     */
    public static function create($className, $level) {
        // Create the event
        $event = new LogEvent();

        // Assign level and message
        $event->_setLevel(LogLevel::create($level));

        // Initialize current date and time (with milliseconds)
        $event->_setDate(DateMilli::create());

        // Initialize trace
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);

        $trace['class'] = $className;
        $trace['file'] = $trace[2]['file'];
        $trace['line'] = $trace[2]['line'];
        $event->_setTrace($trace);

        return $event;
    }

    public function getDate() {
        return $this->_date;
    }

    private function _setDate($date) {
        $this->_date = $date;
    }

    public function getLevel() {
        return $this->_level;
    }

    private function _setLevel(LogLevel $level) {
        $this->_level = $level;
    }

    public function hasMessage() {
        return isset($this->_message);
    }

    public function getMessage() {
        return $this->_message;
    }

    public function setMessage($message) {
        $this->_message = $message;
    }

    public function hasException() {
        return isset($this->_exception);
    }

    public function getException() {
        return $this->_exception;
    }

    public function setException(\Exception $exception) {
        $this->_exception = $exception;
    }

    public function hasContext() {
        return isset($this->_context) && !empty($this->_context);
    }

    public function getContext() {
        return $this->_context;
    }

    public function setContext(array $context) {
        $this->_context = $context;
    }

    public function getTrace() {
        return $this->_trace;
    }

    private function _setTrace(array $trace) {
        $this->_trace = $trace;
    }

}
