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
	 * This class represents the rule definition for a "whitespace" token (i.e. MySQL-style comment), which
	 * the tokenizer will use to tokenize a string.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Tokenizer\Token
	 * @version 2015-08-23
	 */
	class EOLComment extends \Leap\Core\DB\SQL\Tokenizer\Token {

		/**
		 * This variable stores the end-of-line characters.
		 *
		 * @access protected
		 * @var array
		 */
		protected $eol;

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 */
		public function __construct() {
			$this->eol = array("\n", "\r", "\x0C", ''); // http://php.net/manual/en/regexp.reference.escape.php
		}

		/**
		 * This method releases any internal references to an object.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->eol);
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
			if ($char == '#') {
				$start = $position;
				do {
					$position++;
				}
				while( ! in_array(static::char_at($statement, $position, $strlen), $this->eol));
				$position++;
				$size = $position - $start;
				$token = substr($statement, $start, $size);
				$tuple = array(
					'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::whitespace(),
					'token' => $token,
				);
				// var_dump($token);
				return $tuple;
			}
			if ($char == '-') { // "whitespace" token (i.e. SQL-style comment) or "operator" token
				$lookahead = $position + 1;
				$length = $strlen - 1;
				if (($lookahead > $length) OR (static::char_at($statement, $lookahead, $strlen) != '-')) {
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
						'token' => $char,
					);
					$position = $lookahead;
					// var_dump($char);
					return $tuple;
				}
				else {
					while ( ! in_array(static::char_at($statement, $lookahead, $strlen), $this->eol)) {
						$lookahead++;
					}
					$lookahead++;
					$size = min($lookahead, $strlen) - $position;
					$token = substr($statement, $position, $size);
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::whitespace(),
						'token' => $token,
					);
					$position = $lookahead;
					// var_dump($token);
					return $tuple;
				}
			}
			return null;
		}

	}

}