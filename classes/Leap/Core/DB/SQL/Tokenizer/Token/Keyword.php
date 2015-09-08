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
	 * This class represents the rule definition for a "keyword" token, which the tokenizer will use
	 * to tokenize a string.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Tokenizer\Token
	 * @version 2015-08-23
	 */
	class Keyword extends \Leap\Core\DB\SQL\Tokenizer\Token {

		/**
		 * This variable stores a list of reserved keywords.
		 *
		 * @access protected
		 * @var array
		 */
		protected $keywords;

		/* This constructor initializes the class.
		 *
		 * @access public
		 * @param array $keywords                                   a list of reserved keywords
		 */
		public function __construct(array $keywords = array()) {
			$this->keywords = $keywords;
		}

		/**
		 * This method releases any internal references to an object.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->keywords);
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
			if ((($char >= 'a') AND ($char <= 'z')) OR (($char >= 'A') AND ($char <= 'Z')) OR ($char == '_')) {
				$length = $strlen - 1;
				$start = $position;
				$next = '';
				do {
					$position++;
					$next = static::char_at($statement, $position, $strlen);
				}
				while (($position <= $length) AND ((($next >= 'a') AND ($next <= 'z')) OR (($next >= 'A') AND ($next <= 'Z')) OR ($next == '_') OR (($next >= '0') AND ($next <= '9'))));
				$size = $position - $start;
				$token = substr($statement, $start, $size);
				$type = isset($this->keywords[$token])
					? \Leap\Core\DB\SQL\Tokenizer\TokenType::keyword()
					: \Leap\Core\DB\SQL\Tokenizer\TokenType::identifier();
				$tuple = array(
					'type' => $type,
					'token' => $token,
				);
				// var_dump($token);
				return $tuple;
			}
			return null;
		}

	}

}