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
	 * This class represents the rule definition for an "operator" token, which the tokenizer will use
	 * to tokenize a string.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Tokenizer\Token
	 * @version 2014-05-11
	 */
	class Operator extends \Leap\Core\DB\SQL\Tokenizer\Token {

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
			if ($char == '|') { // "operator" token
				$lookahead = $position + 1;
				$next = static::char_at($statement, $lookahead, $strlen);
				if ($next == '|') {
					$lookahead++;
					$size = $lookahead - $position;
					$token = substr($statement, $position, $size);
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
						'token' => $token,
					);
					$position = $lookahead;
					// var_dump($token);
					return $tuple;
				}
				else {
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
						'token' => $char,
					);
					$position = $lookahead;
					// var_dump($char);
					return $tuple;
				}

			}
			else if (($char == '!') OR ($char == '=')) { // "operator" token
				$lookahead = $position + 1;
				$next = static::char_at($statement, $lookahead, $strlen);
				if ($next == '=') {
					$lookahead++;
					$size = $lookahead - $position;
					$token = substr($statement, $position, $size);
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
						'token' => $token,
					);
					$position = $lookahead;
					// var_dump($token);
					return $tuple;
				}
				else {
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
						'token' => $char,
					);
					$position = $lookahead;
					// var_dump($char);
					return $tuple;
				}
			}
			else if (($char == '<') OR ($char == '>')) { // "operator" token
				$lookahead = $position + 1;
				$next = static::char_at($statement, $lookahead, $strlen);
				if (($next == '=') OR ($next == $char) OR (($next == '>') AND ($char == '<'))) {
					$lookahead++;
					$size = $lookahead - $position;
					$token = substr($statement, $position, $size);
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
						'token' => $token,
					);
					$position = $lookahead;
					// var_dump($token);
					return $tuple;
				}
				else {
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
						'token' => $char,
					);
					$position = $lookahead;
					// var_dump($char);
					return $tuple;
				}
			}
			else if ($char == '/') { // "whitespace" token (i.e. C-style comment) or "operator" token
				$lookahead = $position + 1;
				$next = static::char_at($statement, $lookahead, $strlen);
				if ($next != '*') {
					$tuple = array(
						'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
						'token' => $char,
					);
					$position = $lookahead;
					// var_dump($char);
					return $tuple;
				}
			}
			else if (in_array($char, array('+', '*', '%', '&', '~'))) {
				$tuple = array(
					'type' => \Leap\Core\DB\SQL\Tokenizer\TokenType::operator(),
					'token' => $char,
				);
				$position++;
				// var_dump($char);
				return $tuple;
			}
			return null;
		}

	}

}