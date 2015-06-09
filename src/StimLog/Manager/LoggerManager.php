<?php

namespace StimLog\Manager;

/**
 * Class used to manage loggers configuration
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class LoggerManager {

    /**
     * The loggers (configurations)
     * 
     * @var array
     */
    private static $_loggers = array();

    /**
     * Setup the loggers configuration with the given file
     * 
     * @param string $filePath the file path
     */
    public static function setupFromFile($filePath) {

        $loggers = null;

        require($filePath);

        if (!isset($loggers)) {
            throw new LoggerManagerException('The configuration file [' . $filePath . '] must define a variable $loggers');
        }

        if (!is_array($loggers)) {
            throw new LoggerManagerException('The configuration file [' . $filePath . '] must define a variable $loggers as an array');
        }

        self::setupFromArray($loggers);
    }

    /**
     * Setup the loggers configuration with the given array
     * 
     * @param array $loggers the loggers configurations
     */
    public static function setupFromArray(array $loggers) {
        foreach ($loggers as $logger) {
            if (!isset($logger['class'])) {
                throw new LoggerManagerException('Each logger configuration defined in the file [' . $configurationFilePath . '] has to define a "class" property');
            }
            if (!is_string($logger['class'])) {
                throw new LoggerManagerException('Each logger configuration defined in the file [' . $configurationFilePath . '] has to define a "class" property, as a string');
            }
            if (!isset($logger['level'])) {
                throw new LoggerManagerException('Each logger configuration defined in the file [' . $configurationFilePath . '] has to define a "level" property');
            }
            if (!is_string($logger['level'])) {
                throw new LoggerManagerException('Each logger configuration defined in the file [' . $configurationFilePath . '] has to define a "level" property, as a string');
            }
            if (!isset($logger['writers'])) {
                throw new LoggerManagerException('Each logger configuration defined in the file [' . $configurationFilePath . '] has to define a "writers" property');
            }
            if (!is_array($logger['writers'])) {
                throw new LoggerManagerException('Each logger configuration defined in the file [' . $configurationFilePath . '] has to define a "writers" property, as an array');
            }
        }

        self::$_loggers = $loggers;
    }

    /**
     * Get the loggers (configurations)
     * 
     * @return array
     */
    public static function getLoggers() {
        return self::$_loggers;
    }

    /**
     * Return the best logger configuration for the given class name
     * 
     * @param string $className the class name which should be observed by a logger
     * @return array|null
     */
    public static function findBestLoggerConfigurationForClass($className) {
        // The best logger configuration for the given class name
        $bestLogger = null;

        // For each registered logger configuration
        foreach (self::getLoggers() as $logger) {

            // If the current class name corresponds to a logger configuration
            if (strpos($className, $logger['class']) === 0) {
                // Add a "length" property to the current logger configuration
                $logger['length'] = strlen($logger['class']);

                // If no best logger existed, the current one is considered as the best
                if (!isset($bestLogger)) {
                    $bestLogger = $logger;
                }
                // A previous best logger existed
                else {
                    // If the current logger is considered more precise than the best one
                    if ($logger['length'] > $bestLogger['length']) {
                        // The current logger is now considered as the best
                        $bestLogger = $logger;
                    }
                }
            }
        }

        return $bestLogger;
    }

}
