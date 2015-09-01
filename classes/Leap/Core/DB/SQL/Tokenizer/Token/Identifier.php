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

namespace Leap\Core\DB\SQL\Tokenizer\Token {

	/**
	 * This class represents the rule definition for an "identifier" token, which the tokenizer will use
	 * to tokenize a string.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Tokenizer\Token
	 * @version 2015-08-23
	 */
	class Identifier extends \Leap\Core\DB\SQL\Tokenizer\Token {

		/**
		 * This variable stores the opening quote character.
		 *
		 * @access protected
		 * @var string
		 */
		protected $opening;

		/**
		 * This variable stores the closing quote character.
		 *
		 * @access protected
		 * @var string
		 */
		protected $closing;

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 * @param string $opening                                   the opening quote character
		 * @param string $closing                                   the closing quote character
		 */
		public function __construct($opening, $closing) {
			$this->opening = $opening;
			$this->closing = $closing;
		}

		/**
		 * This method releases any internal references to an object.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->opening);
			unset($this->closing);
		}

		/**
		 * This method return a tuple representing the token discovered.
		 *
		 * @access public
		 * @param string &$statement                                the string to be analyzed
		 * @param integer &$position                                the current position being analyzed
		 * @param integer $strlen                                   the length of the string
		 * @return array                                            a tuple representing the token
		 *                                                          discovered
		 */
		public function process(&$statement, &$position, $strlen) {
			$char = static::char_at($statement, $position, $strlen);
			if ($char == $this->opening) {
				$length = $strlen - 1;
				$start = $position;
				do {
					$position++;
				}
				while (($position < $length) AND (static::char_at($statement, $position, $strlen) != $this->closing));
				$position++;
				$size = $position - $start;
				$token = substr($statement, $start, $size);
				$tuple = array(
					'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::identifier(),
					'token' => $token,
				);
				// var_dump($token);
				return $tuple;
			}
			return null;
		}

	}

}