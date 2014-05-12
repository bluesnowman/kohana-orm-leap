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
	 * This class enumerates the different types of tokens used by the tokenizer.
	 *
	 * @access public
	 * @class
	 * @final
	 * @package Leap\Core\DB\SQL\Tokenizer
	 * @version 2014-05-11
	 */
	final class TokenType extends \Leap\Core\Enum {

		/**
		 * This method sets up the enumerations.
		 *
		 * @access public
		 * @static
		 */
		public static function __static() {
			static::$__enums = array(
				new static('dot', 'DOT'),
				new static('error', 'ERROR'),
				new static('hexadecimal', 'HEXADECIMAL'),
				new static('identifier', 'IDENTIFIER'),
				new static('integer', 'NUMBER:INTEGER'),
				new static('keyword', 'KEYWORD'),
				new static('literal', 'LITERAL'),
				new static('operator', 'OPERATOR'),
				new static('parameter', 'PARAMETER'),
				new static('real', 'NUMBER:REAL'),
				new static('terminal', 'TERMINAL'),
				new static('unknown', 'UNKNOWN'),
				new static('whitespace', 'WHITESPACE'),
			);
		}

		/**
		 * This method returns the "dot" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function dot() {
			return static::$__enums[0];
		}

		/**
		 * This method returns the "error" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function error() {
			return static::$__enums[1];
		}

		/**
		 * This method returns the "hexadecimal" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function hexadecimal() {
			return static::$__enums[2];
		}

		/**
		 * This method returns the "identifier" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function identifier() {
			return static::$__enums[3];
		}

		/**
		 * This method returns the "integer" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function integer() {
			return static::$__enums[4];
		}

		/**
		 * This method returns the "keyword" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function keyword() {
			return static::$__enums[5];
		}

		/**
		 * This method returns the "literal" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function literal() {
			return static::$__enums[6];
		}

		/**
		 * This method returns the "operator" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function operator() {
			return static::$__enums[7];
		}

		/**
		 * This method returns the "parameter" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function parameter() {
			return static::$__enums[8];
		}

		/**
		 * This method returns the "real" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function real() {
			return static::$__enums[9];
		}

		/**
		 * This method returns the "terminal" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function terminal() {
			return static::$__enums[10];
		}

		/**
		 * This method returns the "unknown" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function unknown() {
			return static::$__enums[11];
		}

		/**
		 * This method returns the "whitespace" token.
		 *
		 * @access public
		 * @static
		 * @return \Leap\Core\DB\SQL\Tokenizer\TokenType            the token type
		 */
		public static function whitespace() {
			return static::$__enums[12];
		}

	}

}