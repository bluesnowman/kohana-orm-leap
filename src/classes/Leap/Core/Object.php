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

namespace Leap\Core {

	/**
	 * This class acts as the base class for a object.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core
	 * @version 2014-01-25
	 */
	abstract class Object implements \Leap\Core\IObject {

		/**
		 * This method returns a copy this object.
		 *
		 * @access public
		 * @throws \Leap\Core\Throwable\UnimplementedMethod\Exception  indicates the method has not be
		 *                                                             implemented
		 */
		public function __clone() {
			throw new \Leap\Core\Throwable\UnimplementedMethod\Exception('Method ":method" has not been implemented in class ":class."', array(':class' => get_called_class(), ':method' => __FUNCTION__));
		}

		/**
		 * This method dumps information about the object.
		 *
		 * @access public
		 */
		public function __debug() {
			var_dump($this);
		}

		/**
		 * This method returns whether the specified object is equal to the called object.
		 *
		 * @access public
		 * @param IObject $object                                   the object to be evaluated
		 * @return boolean                                          whether the specified object is equal
		 *                                                          to the called object
		 */
		public function __equals($object) {
			return (($object !== null) && ($object instanceof \Leap\Core\IObject) && ((string) serialize($object) == (string) serialize($this)));
		}

		/**
		 * This method returns the name of the runtime class of this object.
		 *
		 * @access public
		 * @return string                                           the name of the runtime class
		 */
		public function __getClass() {
			return get_called_class();
		}

		/**
		 * This method returns the hash code for the object.
		 *
		 * @access public
		 * @return string                                           the hash code for the object
		 */
		public function __hashCode() {
			return spl_object_hash($this);
		}

		/**
		 * This method returns a string that represents the object.
		 *
		 * @access public
		 * @return string                                           a string that represents the object
		 */
		public function __toString() {
			return (string) serialize($this);
		}

	}

}