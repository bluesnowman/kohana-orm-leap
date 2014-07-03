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
	 * This interface defines the contract for an object.
	 *
	 * @access public
	 * @interface
	 * @package Leap\Core
	 * @version 2014-06-10
	 */
	interface IObject {

		/**
		 * This method dumps information about the object.
		 *
		 * @access public
		 */
		public function __debug();

		/**
		 * This method returns whether the specified object is equal to the called object.
		 *
		 * @access public
		 * @param \Leap\Core\IObject $object                        the object to be evaluated
		 * @return boolean                                          whether the specified object is equal
		 *                                                          to the called object
		 */
		public function __equals($object);

		/**
		 * This method returns the name of the runtime class of this object.
		 *
		 * @access public
		 * @return string                                           the name of the runtime class
		 */
		public function __getClass();

		/**
		 * This method returns the hash code for the object.
		 *
		 * @access public
		 * @return string                                           the hash code for the object
		 */
		public function __hashCode();

		/**
		 * This method returns a string that represents the object.
		 *
		 * @access public
		 * @return string                                           a string that represents the object
		 */
		public function __toString();

	}

}