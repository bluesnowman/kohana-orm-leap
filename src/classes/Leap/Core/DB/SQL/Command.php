<?php

/**
 * Copyright © 2011–2014 Spadefoot Team.
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

namespace Leap\Core\DB\SQL {

	/**
	 * This class represents an SQL command.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL
	 * @version 2014-07-04
	 */
	class Command extends \Leap\Core\Object {

		/**
		 * This variable stores the data associated with the command.
		 *
		 * @access protected
		 * @var string
		 */
		protected $data;

		/**
		 * This constructor initializes the class with the specified text.
		 *
		 * @access public
		 * @param string $text                                      the text of the command
		 */
		public function __construct($text = '') {
			$this->data = array();
			$this->data['text'] = $text;
		}

		/**
		 * This method returns the value associated with the specified property.
		 *
		 * @access public
		 * @override
		 * @param string $name                                      the name of the property
		 * @return mixed                                            the value of the property
		 * @throws \Leap\Core\Throwable\InvalidProperty\Exception   indicates that the specified property is
		 *                                                          either inaccessible or undefined
		 */
		public function __get($name) {
			switch ($name) {
				case 'text':
					return $this->data[$name];
				default:
					throw new \Leap\Core\Throwable\InvalidProperty\Exception('Message: Unable to get the specified property. Reason: Property :name is either inaccessible or undefined.', array(':name' => $name));
			}
		}

		/**
		 * This method sets the value for the specified key.
		 *
		 * @access public
		 * @override
		 * @param string $name                                      the name of the property
		 * @param mixed $value                                      the value of the property
		 * @throws \Leap\Core\Throwable\InvalidProperty\Exception   indicates that the specified property is
		 *                                                          either inaccessible or undefined
		 */
		public function __set($name, $value) {
			switch ($name) {
				case 'text':
					$this->data[$name] = (string) $value;
					break;
				default:
					throw new \Leap\Core\Throwable\InvalidProperty\Exception('Message: Unable to set the specified property. Reason: Property :name is either inaccessible or undefined.', array(':name' => $name));
			}
		}

		/**
		 * This method returns a string that represents the object.
		 *
		 * @access public
		 * @return string                                           a string that represents the object
		 */
		public function __toString() {
			return $this->data['text'];
		}

		/**
		 * This method trims the semicolon off an SQL command.
		 *
		 * @access protected
		 * @static
		 * @param string $text					                    the SQL command
		 * @return string                                           the SQL command after being trimmed
		 */
		public static function trim($text) {
			return trim($text, "; \t\n\r\0\x0B");
		}

	}

}