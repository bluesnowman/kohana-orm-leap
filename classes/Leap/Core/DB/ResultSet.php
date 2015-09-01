<?php

/**
 * Copyright © 2011–2015 Spadefoot Team.
 *
 * Unless otherwise noted, Leap is licensed under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License
 * at:
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Leap\Core\DB {

	/**
	 * This class represents a result set.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB
	 * @version 2015-08-23
	 */
	class ResultSet extends \Leap\Core\Object implements \ArrayAccess, \Countable, \Iterator, \SeekableIterator, \Leap\Core\GC\IDisposable {

		/**
		 * This variable stores the current position in the records array.
		 *
		 * @access protected
		 * @var integer
		 */
		protected $position;

		/**
		 * This variable stores the records.
		 *
		 * @access protected
		 * @var array
		 */
		protected $records;

		/**
		 * This variable stores the length of the records array.
		 *
		 * @access protected
		 * @var integer
		 */
		protected $size;

		/**
		 * This variable stores the return type being used.
		 *
		 * @access protected
		 * @var string
		 */
		protected $type;

		/**
		 * This method initializes the class by wrapping the result set so that all database
		 * result sets are accessible alike.
		 *
		 * @access public
		 * @param mixed $buffer                                     either an array of records or a data reader
		 * @param string $type                                      the return type being used
		 */
		public function __construct($buffer, $type = 'array') {
			if (is_array($buffer)) {
				$this->records = $buffer;
				$this->size = count($buffer);
			}
			else {
				$this->records = array();
				$this->size = 0;
				if (is_object($buffer) AND ($buffer instanceof \Leap\Core\DB\SQL\DataReader)) {
					while ($buffer->read()) {
						$this->records[] = $buffer->row($type);
						$this->size++;
					}
					$buffer->dispose();
				}
			}
			$this->position = 0;
			$this->type = $type;
		}

		/**
		 * This method releases any internal references to an object.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->position);
			unset($this->records);
			unset($this->size);
			unset($this->type);
		}

		/**
		 * This method returns an array of records of the desired object type.
		 *
		 * @access public
		 * @return array                                            an array of records
		 */
		public function as_array() {
			return $this->records;
		}

		/**
		 * This method will create an instance of the CSV class using the data contained
		 * in the result set.
		 *
		 * @access public
		 * @param array $config                                     the configuration array
		 * @return \Leap\Core\Data\Serialization\CSV                an instance of the CSV class
		 */
		public function as_csv(Array $config = array()) {
			$csv = new \Leap\Core\Data\Serialization\CSV($config);
			if ($this->is_loaded()) {
				switch ($this->type) {
					case 'array':
					case 'object':
						foreach ($this->records as $record) {
							$csv->add_row( (array) $record);
						}
					break;
					default:
						if (class_exists($this->type)) {
							if (($this->records[0] instanceof \Leap\Core\DB\ORM\Model) OR method_exists($this->records[0], 'as_array')) {
								foreach ($this->records as $record) {
									$csv->add_row($record->as_array());
								}
							}
							else if ($this->records[0] instanceof \Iterator) {
								foreach ($this->records as $record) {
									$row = array();
									foreach ($record as $column) {
										$row[] = $column;
									}
									$csv->add_row($row);
								}
							}
							else {
								foreach ($this->records as $record) {
									$csv->add_row(get_object_vars($record));
								}
							}
						}
					break;
				}
			}
			return $csv;
		}

		/**
		 * This method returns the total number of records contained in result set.
		 *
		 * @access public
		 * @override
		 * @return integer                                          the total number of records
		 */
		public function count() {
			return $this->size;
		}

		/**
		 * This method returns the current record.
		 *
		 * @access public
		 * @override
		 * @return mixed                                            the current record
		 */
		public function current() {
			return isset($this->records[$this->position]) ? $this->records[$this->position] : NULL;
		}

		/**
		 * This method assists with freeing, releasing, and resetting un-managed resources.
		 *
		 * @access public
		 * @param boolean $disposing                                whether managed resources can be disposed
		 *                                                          in addition to un-managed resources
		 */
		public function dispose($disposing = TRUE) {
			$this->records = array();
			$this->position = 0;
			$this->size = 0;
		}

		/**
		 * This method returns a record either at the current position or
		 * the specified position.
		 *
		 * @access public
		 * @param integer $index                                    the record's index
		 * @return mixed                                            the record
		 */
		public function fetch($index = -1) {
			settype($index, 'integer');
			if ($index < 0) {
				$index = $this->position;
				$this->position++;
			}

			if (isset($this->records[$index])) {
				return $this->records[$index];
			}

			return FALSE;
		}

		/**
		 * This method returns the value for the named column from the current record.
		 *
		 *     // Gets the value of "id" from the current record
		 *     $id = $results->get('id');
		 *
		 * @access public
		 * @param string $name                                      the name of the column
		 * @param mixed $default                                    the default value should the column
		 *                                                          does not exist
		 * @return mixed                                            the value for the named column
		 */
		public function get($name, $default = NULL) {
			$record = $this->current();

			if (is_object($record)) {
				try {
					$value = $record->{$name};
					if ($value !== NULL) {
						return $value;
					}
				}
				catch (\Exception $ex) {}
			}
			else if (is_array($record) AND isset($record[$name])) {
				return $record[$name];
			}

			return $default;
		}

		/**
		 * This method returns whether any records were loaded.
		 *
		 * @access public
		 * @return boolean                                          whether any records were loaded
		 */
		public function is_loaded() {
			return ($this->size > 0);
		}

		/**
		 * This method returns the position to the current record.
		 *
		 * @access public
		 * @override
		 * @return integer                                          the position of the current record
		 */
		public function key() {
			return $this->position;
		}

		/**
		 * This method moves forward the position to the next record, lazy loading only
		 * when necessary.
		 *
		 * @access public
		 * @override
		 */
		public function next() {
			$this->position++;
		}

		/**
		 * This method determines whether an offset exists.
		 *
		 * @access public
		 * @override
		 * @param integer $offset                                   the offset to be evaluated
		 * @return boolean                                          whether the requested offset exists
		 */
		public function offsetExists($offset) {
			return isset($this->records[$offset]);
		}

		/**
		 * This method gets value at the specified offset.
		 *
		 * @access public
		 * @override
		 * @param integer $offset                                   the offset to be fetched
		 * @return mixed                                            the value at the specified offset
		 */
		public function offsetGet($offset) {
			return isset($this->records[$offset]) ? $this->records[$offset] : NULL;
		}

		/**
		 * This method sets the specified value at the specified offset.
		 *
		 * @access public
		 * @override
		 * @param integer $offset                                      the offset to be set
		 * @param mixed $value                                         the value to be set
		 * @throws \Leap\Core\Throwable\UnimplementedMethod\Exception  indicates the result cannot be modified
		 */
		public function offsetSet($offset, $value) {
			throw new \Leap\Core\Throwable\UnimplementedMethod\Exception('Message: Invalid call to member function. Reason: Result set cannot be modified.', array(':offset' => $offset, ':value' => $value));
		}

		/**
		 * This method allows for the specified offset to be unset.
		 *
		 * @access public
		 * @override
		 * @param integer $offset                                      the offset to be unset
		 * @throws \Leap\Core\Throwable\UnimplementedMethod\Exception  indicates the result cannot be modified
		 */
		public function offsetUnset($offset) {
			throw new \Leap\Core\Throwable\UnimplementedMethod\Exception('Message: Invalid call to member function. Reason: Result set cannot be modified.', array(':offset' => $offset));
		}

		/**
		 * This method returns the current iterator position.
		 *
		 * @access public
		 * @override
		 * @return integer                                          the current iterator position
		 */
		public function position() {
			return $this->position;
		}

		/**
		 * This method rewinds the iterator back to starting position.
		 *
		 * @access public
		 * @override
		 */
		public function rewind() {
			$this->position = 0;
		}

		/**
		 * This method sets the position pointer to the seeked position.
		 *
		 * @access public
		 * @override
		 * @param integer $position                                 the seeked position
		 * @throws \Leap\Core\Throwable\OutOfBounds\Exception       indicates that the seeked position
		 *                                                          is out of bounds
		 */
		public function seek($position) {
			if ( ! isset($this->records[$position])) {
				throw new \Leap\Core\Throwable\OutOfBounds\Exception('Message: Invalid array position. Reason: The specified position is out of bounds.', array(':position' => $position, ':count' => $this->size));
			}
			$this->position = $position;
		}

		/**
		 * This method checks if the current iterator position is valid.
		 *
		 * @access public
		 * @override
		 * @return boolean                                          whether the current iterator position is valid
		 */
		public function valid() {
			return isset($this->records[$this->position]);
		}

	}

}