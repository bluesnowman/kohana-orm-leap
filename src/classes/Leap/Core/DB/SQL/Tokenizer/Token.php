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

namespace Leap\Core\DB\SQL\Tokenizer {

	/**
	 * This class represents the rule definition for a token, which the tokenizer will use
	 * to tokenize a string.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Tokenizer
	 * @version 2014-05-10
	 */
	abstract class Token extends \Leap\Core\Object {

		/**
		 * This method return a tuple representing the token discovered.
		 *
		 * @abstract
		 * @access public
		 * @param string &$statement                                the string to be analyzed
		 * @param integer &$position                                the current position being analyzed
		 * @param integer $strlen                                   the length of the string
		 * @return array                                            a tuple representing the token
		 *                                                          discovered
		 */
		public abstract function process(&$statement, &$position, $strlen);

		/**
		 * This method returns the character at the specified position.
		 *
		 * @access protected
		 * @static
		 * @param string &$string                                   the string to be used
		 * @param integer $index                                    the character's index
		 * @param integer $length                                   the string's length
		 * @return char                                             the character at the specified index
		 */
		protected static function char_at(&$string, $index, $length) {
			return isset($string[$index]) ? $string[$index] : '';
		}

	}

}