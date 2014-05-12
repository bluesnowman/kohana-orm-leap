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

namespace Leap\Core\DB\SQL\Tokenizer\Token {

	/**
	 * This class represents the rule definition for a "whitespace" token (i.e. C-style comment), which
	 * the tokenizer will use to tokenize a string.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Tokenizer\Token
	 * @version 2014-05-10
	 */
	class BlockComment extends \Leap\Core\DB\SQL\Tokenizer\Token {

		/**
		 * This variable stores the opening 2-character sequence.
		 *
		 * @access protected
		 * @var string
		 */
		protected $opening;

		/**
		 * This variable stores the closing 2-character sequence.
		 *
		 * @access protected
		 * @var string
		 */
		protected $closing;

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 * @param string $opening                                   the opening 2-character sequence
		 * @param string $closing                                   the closing 2-character sequence
		 */
		public function __construct($opening, $closing) {
			$this->opening = $opening;
			$this->closing = $closing;
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
			if ($char == $this->opening[0]) {
				$lookahead = $position + 1;
				$next = static::char_at($statement, $lookahead, $strlen);
				if ($next == $this->opening[1]) {
					$lookahead += 2;
					while ( ! ((static::char_at($statement, $lookahead - 1, $strlen) == $this->closing[0]) AND (static::char_at($statement, $lookahead, $strlen) == $this->closing[1]))) {
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