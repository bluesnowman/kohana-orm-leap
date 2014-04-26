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

namespace Leap\Core\Web\HTTP {

	/**
	 * This class represents a "Content-Disposition" header string.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core
	 * @version 2014-04-26
	 *
	 * @see http://msdn.microsoft.com/en-us/library/system.net.mime.contentdisposition%28v=vs.110%29.aspx
	 * @see http://tools.ietf.org/html/rfc2183
	 */
	class ContentDisposition extends \Leap\Core\Object {

		/**
		 * This variable stores whether the disposition is inline.
		 *
		 * @access protected
		 * @var boolean
		 */
		protected $type;

		/**
		 * This varaible stores the parameters related to the disposition.
		 *
		 * @access protected
		 * @var array
		 */
		protected $parameters;

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 */
		public function __construct() {
			$this->type = true;
			$this->parameters = array(
				'file_name' => null
			);
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
				case 'creation_date':
				case 'file_name':
				case 'modification_date':
				case 'read_date':
				case 'size':
					return $this->parameters[$name];
				case 'inline':
					return $this->type;
				case 'type':
					return ($this->type) ? 'inline' : 'attachment';
				default:
					throw new \Leap\Core\Throwable\InvalidProperty\Exception('Message: Unable to get the specified property. Reason: Property :key is either inaccessible or undefined.', array(':key' => $name));
			}
		}

		/**
		 * This method determines whether a specific property has been set.
		 *
		 * @access public
		 * @override
		 * @param string $name                                      the name of the property
		 * @return boolean                                          indicates whether the specified property
		 *                                                          has been set
		 */
		public function __isset($name) {
			if (isset($this->parameters[$name])) {
				return (FALSE === empty($this->parameters[$name]));
			}
			return ($name != 'type');
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
				case 'file_name':
					$file_name = preg_split('!(\?.*|/)!', $value, -1, PREG_SPLIT_NO_EMPTY);
					$this->parameters[$name] = $file_name[count($file_name) - 1];
					break;
				case 'creation_date':
				case 'modification_date':
				case 'read_date':
					$this->parameters[$name] = $value;
					break;
				case 'size':
					$this->parameters[$name] = (int) $value;
					break;
				case 'inline':
					$this->type = (bool) $value;
					break;
				case 'type':
					$this->type = (bool) preg_match('/^inline$/i', $value);
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
			if ( ! $this->type) {
				$buffer = "Content-Disposition: attachment";
				foreach ($this->parameters as $name => &$value) {
					if ($name == 'file_name') {
						if (empty($this->parameters[$name])) {
							$value = date('YmdHis') . '.txt';
						}
						$buffer .= '; filename="' . $value . '"';
					}
					else if (!empty($this->parameters[$name])) {
						$buffer .= '; ' . str_replace('_', '-', $name) . '="' . $value . '"';
					}
				}
				return $buffer;
			}
			return 'Content-Disposition: inline';
		}

	}

}