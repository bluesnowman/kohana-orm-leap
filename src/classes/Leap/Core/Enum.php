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

namespace Leap\Core {

	/**
	 * This class represents a typed enumeration.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core
	 * @version 2014-05-11
	 */
	abstract class Enum extends \Leap\Core\Object {

		/**
		 * This variable stores the enumerations.
		 *
		 * @access protected
		 * @static
		 * @var array                                               an indexed array of the enumerations
		 */
		protected static $__enums = array();

		/**
		 * This variable stores the next ordinal value to be assigned.
		 *
		 * @access protected
		 * @static
		 * @var integer                                             the next ordinal value to be assigned
		 */
		protected static $__ordinals = 0;

		/**
		 * This variable stores the name assigned to the enumeration.
		 *
		 * @access protected
		 * @var string                                              the name of the enumeration
		 */
		protected $__name;

		/**
		 * This variable stores the ordinal value assigned to the enumeration.
		 *
		 * @access protected
		 * @var integer                                             the ordinal value assigned to the enumeration
		 */
		protected $__ordinal;

		/**
		 * This variable stores the value assigned to the enumeration.
		 *
		 * @access protected
		 * @var mixed                                               the value assigned to the enumeration
		 */
		protected $__value;

		/**
		 * This method is purposely disabled to prevent the cloning of the enumeration.
		 *
		 * @access public
		 * @final
		 * @throws \Leap\Core\Throwable\CloneNotSupported\Exception indicates that the object cannot
		 *                                                          be cloned
		 */
		public final function __clone() {
			throw new \Leap\Core\Throwable\CloneNotSupported\Exception('Unable to clone object. Class may not be cloned and should be treated as immutable.');
		}

		/**
		 * This method creates an enumeration.  It should only be called in the "__static" constructor
		 * to initialize the enumeration.
		 *
		 * @access protected
		 * @param string $name                                      the name of the enumeration
		 * @param mixed $value                                      the value to be assigned to the enumeration
		 */
		protected function __construct($name, $value) {
			$this->__name = '' . $name;
			$this->__value = $value;
			$this->__ordinal = static::$__ordinals++;
		}

		/**
		 * This method evaluates whether the specified objects is equivalent to the current
		 * object.
		 *
		 * @access public
		 * @param mixed $object                                     the object to be evaluated
		 * @return boolean                                          whether the specified object is equivalent
		 *                                                          to the current object
		 */
		public function __equals($object) {
			return (($object !== null) && ($object instanceof \Leap\Core\Enum) && ($object->__ordinal() == $this->__ordinal()));
		}

		/**
		 * This method returns the name assigned to the enumeration.
		 *
		 * @access public
		 * @return string                                           the name assigned to the enumeration
		 */
		public function __name() {
			return $this->__name;
		}

		/**
		 * This method returns a string representing the enumeration.
		 *
		 * @access public
		 * @return string                                           a string representing the enumeration
		 */
		public function __toString() {
			return '' . $this->__value;
		}

		/**
		 * This method returns the ordinal value assigned to the enumeration.
		 *
		 * @access public
		 * @return integer                                          the ordinal value assigned to the enumeration
		 */
		public function __ordinal() {
			return $this->__ordinal;
		}

		/**
		 * This method returns the value assigned to the enumeration.
		 *
		 * @access public
		 * @return mixed                                            the value assigned to the enumeration
		 */
		public function __value() {
			return $this->__value;
		}

		/**
		 * This method returns an indexed array containing the values assigned to the enumerations.
		 *
		 * @access public
		 * @static
		 * @return array                                            an indexed array containing the values assigned
		 *                                                          to the enumerations
		 */
		public static function __values() {
			$values = array();
			foreach (static::$__enums as $enum) {
				$values[] = $enum->__value();
			}
			return $values;
		}

	}

}
