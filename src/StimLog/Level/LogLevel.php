<?php

namespace StimLog\Level;

use Psr\Log\InvalidArgumentException;

/**
 * Class used to manage log levels
 * 
 * The class follows RFC 5424
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class LogLevel {

    /**
     * Debug-level messages
     * 
     * @var int
     */
    const DEBUG = 100;

    /**
     * Informational messages
     * 
     * @var int
     */
    const INFO = 200;

    /**
     * Normal but significant conditions
     * 
     * @var int
     */
    const NOTICE = 300;

    /**
     * Warning conditions
     * 
     * @var int
     */
    const WARNING = 400;

    /**
     * Error conditions
     * 
     * @var int
     */
    const ERROR = 500;

    /**
     * Critical conditions
     * 
     * @var int
     */
    const CRITICAL = 600;

    /**
     * Action must be taken immediately
     * 
     * @var int
     */
    const ALERT = 700;

    /**
     * System is unusable
     * 
     * @var int
     */
    const EMERGENCY = 800;

    private static $_levels = array(
        LogLevel::DEBUG => 'debug',
        LogLevel::INFO => 'info',
        LogLevel::NOTICE => 'notice',
        LogLevel::WARNING => 'warning',
        LogLevel::ERROR => 'error',
        LogLevel::CRITICAL => 'critical',
        LogLevel::ALERT => 'alert',
        LogLevel::EMERGENCY => 'emergency'
    );
    private $_level;

    private function __construct($level) {
        $this->_level = $level;
    }

    public static function isValid($level) {
        return isset(self::$_levels[$level]);
    }

    public static function create($level) {
        if (!self::isValid($level)) {
            throw new InvalidArgumentException("The given log level [$level] is not a valid one");
        }
        return new LogLevel($level);
    }

    public static function createFromCode($code) {
        $reverted = array_flip(self::$_levels);
        if (isset($reverted[$code])) {
            return self::create($reverted[$code]);
        }
        throw new InvalidArgumentException("The given log code [$code] is not a valid one");
    }

    public function toString() {
        return self::$_levels[$this->_level];
    }

    public function toInt() {
        return $this->_level;
    }

}
