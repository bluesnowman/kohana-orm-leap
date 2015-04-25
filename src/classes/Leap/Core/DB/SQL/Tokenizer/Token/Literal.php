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
	 * This class represents the rule definition for a "literal" token, which the tokenizer will use
	 * to tokenize a string.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Tokenizer\Token
	 * @version 2014-05-10
	 */
	class Literal extends \Leap\Core\DB\SQL\Tokenizer\Token {

		/**
		 * This variable stores the quotation mark that signals the beginning and end of the token.
		 *
		 * @access protected
		 * @var string
		 */
		protected $quotation;

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 * @param string $quotation                                 the quotation mark that will signal
		 *                                                          the beginning and end of the token
		 */
		public function __construct($quotation) {
			$this->quotation = $quotation;
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
			if ($char == $this->quotation) {
				$lookahead = $position + 1;
				$length = $strlen - 1;
				while ($lookahead <= $length) {
					if (static::char_at($statement, $lookahead, $strlen) == $this->quotation) {
						if (($lookahead == $length) OR (static::char_at($statement, $lookahead + 1, $strlen) != $this->quotation)) {
							$lookahead++;
							break;
						}
						$lookahead++;
					}
					$lookahead++;
				}
				$size = $lookahead - $position;
				$token = substr($statement, $position, $size);
				$tuple = array(
					'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::literal(),
					'token' => $token,
				);
				$position = $lookahead;
				// var_dump($token);
				return $tuple;
			}
			return null;
		}

	}

}