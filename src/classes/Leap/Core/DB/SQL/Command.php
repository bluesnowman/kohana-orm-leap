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
	 * @version 2014-04-26
	 */
	class Command extends \Leap\Core\Object {

		/**
		 * This variable stores the text.
		 *
		 * @access protected
		 * @var string
		 */
		protected $text;

		/**
		 * This constructor initializes the class with the specified text.
		 *
		 * @access public
		 * @param string $text                                      the text of the command
		 */
		public function __construct($text) {
			$this->text = $text;
		}

		/**
		 * This method returns a string that represents the object.
		 *
		 * @access public
		 * @return string                                           a string that represents the object
		 */
		public function __toString() {
			return $this->text;
		}

	}

}