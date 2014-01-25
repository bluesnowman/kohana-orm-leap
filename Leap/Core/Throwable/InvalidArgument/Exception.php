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

namespace Leap\Core\Throwable\InvalidArgument {

	/**
	 * This class indicates that an argument does not match with the expected value.
	 *
	 * @package Leap
	 * @category Throwable
	 * @version 2012-12-05
	 *
	 * @abstract
	 */
	abstract class Exception extends \InvalidArgumentException {

		/**
		 * This method instantiates the exception with the specified message,
		 * variables, and code.
		 *
		 * @access public
		 * @param string $message                        the message
		 * @param array $variables                       the variables
		 * @param integer $code                          the code
		 * @return Throwable\InvalidArgument\Exception   the exception
		 */
		public function __construct($message, Array $variables = NULL, $code = 0) {
			// Set the message
			$message = __($message, $variables);

			// Pass the message to the parent
			parent::__construct($message, $code);
		}

		/**
		 * This method returns a string for this object.
		 *
		 * @access public
		 * @override
		 * @return string                                the string for this object
		 */
		public function __toString() {
			return \Kohana\Exception::text($this);
		}

	}

}