<?php
namespace StimLog\Writer\Formatter;

use StimLog\Event\LogEvent;
/**
 * Abstract class for log writers
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
interface LogFormatter {
	public function formatEvent(LogEvent $event);
	
	public function formatException(\Exception $e);
}