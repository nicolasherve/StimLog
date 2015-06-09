<?php
namespace StimLog\Writer;

use StimLog\Writer\Formatter\TextFormatter;
use StimLog\Event\LogEvent;
/**
 * Class used to write logs into a file
 *
 * @author     Nicolas Hervé <nherve@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php
 */
class FileWriter extends LogWriter {
	
	protected $_filePath;
	
	public function __construct() {
		$this->setFormatter(new TextFormatter());
	}
	
	public function processEvent(LogEvent $event) {
		// Format the data to store
		$data = $this->_formatter->formatEvent($event);
		
		// Write the data to the file
		$this->_writeData($data);
	}
	
	private function _writeData($data) {
		// If the file is not defined
		if (!isset($this->_filePath)) {
		    throw new FileWriterException('The file is not defined for the FileWriter instance');
		}
	    
	    // Append the data to the file
		if (!$fp = fopen($this->_filePath, 'a')) {
			throw new FileWriterException('The access to file ['.$this->_filePath.'] cannot be executed');
		}
		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	
	public function getFilePath() {
		return $this->_filePath;
	}
	
	public function setFilePath($filePath) {
		$this->_filePath = $filePath;
	}
}