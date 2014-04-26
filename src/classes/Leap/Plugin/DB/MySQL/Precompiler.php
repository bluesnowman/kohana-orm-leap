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

namespace Leap\Plugin\DB\MySQL {

	/**
	 * This class provides a set of functions for preparing MySQL expressions.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\MySQL
	 * @version 2014-01-30
	 */
	class Precompiler extends \Leap\Core\DB\SQL\Precompiler {

		/**
		 * This constant represents a closing identifier quote character.
		 *
		 * @access public
		 * @static
		 * @const string
		 */
		const _CLOSING_QUOTE_CHARACTER_ = '`';

		/**
		 * This constant represents an opening identifier quote character.
		 *
		 * @access public
		 * @static
		 * @const string
		 */
		const _OPENING_QUOTE_CHARACTER_ = '`';

		/**
		 * This method prepares the specified expression as an alias.
		 *
		 * @access public
		 * @override
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public function prepare_alias($expr) {
			if ( ! is_string($expr)) {
				throw new \Leap\Core\Throwable\InvalidArgument\Exception('Message: Invalid alias token specified. Reason: Token must be a string.', array(':expr' => $expr));
			}
			return static::_OPENING_QUOTE_CHARACTER_ . trim(preg_replace('/[^a-z0-9$_ ]/i', '', $expr)) . static::_CLOSING_QUOTE_CHARACTER_;
		}

		/**
		 * This method prepares the specified expression as an identifier column.
		 *
		 * @access public
		 * @override
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 *
		 * @see http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
		 * @see http://www.ispirer.com/wiki/sqlways/mysql/identifiers
		 */
		public function prepare_identifier($expr) {
			if ($expr instanceof \Leap\Plugin\DB\MySQL\Select\Builder) {
				return \Leap\Core\DB\SQL\Builder::_OPENING_PARENTHESIS_ . $expr->statement(FALSE) . \Leap\Core\DB\SQL\Builder::_CLOSING_PARENTHESIS_;
			}
			else if ($expr instanceof \Leap\Core\DB\SQL\Expression) {
				return $expr->value($this);
			}
			else if ( ! is_string($expr)) {
				throw new \Leap\Core\Throwable\InvalidArgument\Exception('Message: Invalid identifier expression specified. Reason: Token must be a string.', array(':expr' => $expr));
			}
			else if (preg_match('/^SELECT.*$/i', $expr)) {
				$expr = rtrim($expr, "; \t\n\r\0\x0B");
				return \Leap\Core\DB\SQL\Builder::_OPENING_PARENTHESIS_ . $expr . \Leap\Core\DB\SQL\Builder::_CLOSING_PARENTHESIS_;
			}
			$parts = explode('.', $expr);
			foreach ($parts as &$part) {
				$part = static::_OPENING_QUOTE_CHARACTER_ . trim(preg_replace('/[^a-z0-9$_ ]/i', '', $part)) . static::_CLOSING_QUOTE_CHARACTER_;
			}
			$expr = implode('.', $parts);
			return $expr;
		}

		/**
		 * This method prepares the specified expression as a join type.
		 *
		 * @access public
		 * @override
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 *
		 * @see http://dev.mysql.com/doc/refman/5.0/en/join.html
		 */
		public function prepare_join($expr) {
			if (is_string($expr)) {
				$expr = strtoupper($expr);
				switch ($expr) {
					case \Leap\Core\DB\SQL\JoinType::_CROSS_:
					case \Leap\Core\DB\SQL\JoinType::_INNER_:
					case \Leap\Core\DB\SQL\JoinType::_LEFT_:
					case \Leap\Core\DB\SQL\JoinType::_LEFT_OUTER_:
					case \Leap\Core\DB\SQL\JoinType::_RIGHT_:
					case \Leap\Core\DB\SQL\JoinType::_RIGHT_OUTER_:
					case \Leap\Core\DB\SQL\JoinType::_NATURAL_:
					case \Leap\Core\DB\SQL\JoinType::_NATURAL_LEFT_:
					case \Leap\Core\DB\SQL\JoinType::_NATURAL_LEFT_OUTER_:
					case \Leap\Core\DB\SQL\JoinType::_NATURAL_RIGHT_:
					case \Leap\Core\DB\SQL\JoinType::_NATURAL_RIGHT_OUTER_:
					case \Leap\Core\DB\SQL\JoinType::_STRAIGHT_:
						return $expr;
					break;
				}
			}
			throw new \Leap\Core\Throwable\InvalidArgument\Exception('Message: Invalid join type token specified. Reason: Token must exist in the enumerated set.', array(':expr' => $expr));
		}

		/**
		 * This method prepares the specified expression as a operator.
		 *
		 * @access public
		 * @override
		 * @param string $expr                                      the expression to be prepared
		 * @param string $group                                     the operator grouping
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public function prepare_operator($expr, $group) {
			if (is_string($group) AND is_string($expr)) {
				$group = strtoupper($group);
				$expr = strtoupper($expr);
				if ($group == 'COMPARISON') {
					switch ($expr) {
						case \Leap\Core\DB\SQL\Operator::_NOT_EQUAL_TO_:
							$expr = \Leap\Core\DB\SQL\Operator::_NOT_EQUIVALENT_;
						case \Leap\Core\DB\SQL\Operator::_NOT_EQUIVALENT_:
						case \Leap\Core\DB\SQL\Operator::_EQUAL_TO_:
						case \Leap\Core\DB\SQL\Operator::_BETWEEN_:
						case \Leap\Core\DB\SQL\Operator::_NOT_BETWEEN_:
						case \Leap\Core\DB\SQL\Operator::_LIKE_:
						case \Leap\Core\DB\SQL\Operator::_NOT_LIKE_:
						case \Leap\Core\DB\SQL\Operator::_LESS_THAN_:
						case \Leap\Core\DB\SQL\Operator::_LESS_THAN_OR_EQUAL_TO_:
						case \Leap\Core\DB\SQL\Operator::_GREATER_THAN_:
						case \Leap\Core\DB\SQL\Operator::_GREATER_THAN_OR_EQUAL_TO_:
						case \Leap\Core\DB\SQL\Operator::_IN_:
						case \Leap\Core\DB\SQL\Operator::_NOT_IN_:
						case \Leap\Core\DB\SQL\Operator::_IS_:
						case \Leap\Core\DB\SQL\Operator::_IS_NOT_:
						case \Leap\Core\DB\SQL\Operator::_REGEX_:
						case \Leap\Core\DB\SQL\Operator::_NOT_REGEX_:
							return $expr;
						break;
					}
				}
				else if ($group == 'SET') {
					switch ($expr) {
						case \Leap\Core\DB\SQL\Operator::_UNION_:
						case \Leap\Core\DB\SQL\Operator::_UNION_ALL_:
						case \Leap\Core\DB\SQL\Operator::_UNION_DISTINCT_:
							return $expr;
						break;
					}
				}
			}
			throw new \Leap\Core\Throwable\InvalidArgument\Exception('Message: Invalid operator token specified. Reason: Token must exist in the enumerated set.', array(':group' => $group, ':expr' => $expr));
		}

		/**
		 * This method prepare the specified expression as a ordering token.
		 *
		 * @access public
		 * @override
		 * @param string $column                                    the column to be sorted
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          column will sorted either in ascending or
		 *                                                          descending order
		 * @param string $nulls                                     the weight to be given to null values
		 * @return string                                           the prepared clause
		 *
		 * @see http://forums.mysql.com/read.php?10,208709,208927
		 */
		public function prepare_ordering($column, $ordering, $nulls) {
			$column = $this->prepare_identifier($column);
			switch (strtoupper($ordering)) {
				case 'DESC':
					$ordering = 'DESC';
				break;
				case 'ASC':
				default:
					$ordering = 'ASC';
				break;
			}
			$expr = '';
			switch (strtoupper($nulls)) {
				case 'FIRST':
					$expr .= "CASE WHEN {$column} IS NULL THEN 0 ELSE 1 END, ";
				break;
				case 'LAST':
					$expr .= "CASE WHEN {$column} IS NULL THEN 1 ELSE 0 END, ";
				break;
			}
			$expr .= "{$column} {$ordering}";
			return $expr;
		}

		/**
		 * This method prepares the specified expression as a value.
		 *
		 * @access public
		 * @override
		 * @param string $expr                                      the expression to be prepared
		 * @param char $escape                                      the escape character
		 * @return string                                           the prepared expression
		 */
		public function prepare_value($expr, $escape = NULL) {
			if ($expr === NULL) {
				return 'NULL';
			}
			else if ($expr === TRUE) {
				return "'1'";
			}
			else if ($expr === FALSE) {
				return "'0'";
			}
			else if (is_array($expr)) {
				$buffer = array();
				foreach ($expr as $value) {
					$buffer[] = $this->prepare_value($value, $escape);
				}
				return \Leap\Core\DB\SQL\Builder::_OPENING_PARENTHESIS_ . implode(', ', $buffer) . \Leap\Core\DB\SQL\Builder::_CLOSING_PARENTHESIS_;
			}
			else if (is_object($expr)) {
				if ($expr instanceof \Leap\Plugin\DB\MySQL\Select\Builder) {
					return \Leap\Core\DB\SQL\Builder::_OPENING_PARENTHESIS_ . $expr->statement(FALSE) . \Leap\Core\DB\SQL\Builder::_CLOSING_PARENTHESIS_;
				}
				else if ($expr instanceof \Leap\Core\DB\SQL\Expression) {
					return $expr->value($this);
				}
				else if ($expr instanceof \Leap\Core\Data\ByteString) {
					return $expr->as_hexcode("x'%s'");
				}
				else if ($expr instanceof \Leap\Core\Data\BitField) {
					return $expr->as_binary("b'%s'");
				}
				else {
					return static::prepare_value( (string) $expr); // Convert the object to a string
				}
			}
			else if (is_integer($expr)) {
				return (int) $expr;
			}
			else if (is_double($expr)) {
				return sprintf('%F', $expr);
			}
			else if (is_string($expr) AND preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}(\s[0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $expr)) {
				return "'{$expr}'";
			}
			else if ($expr === '') {
				return "''";
			}
			else {
				return \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source)->quote($expr, $escape);
			}
		}

		/**
		 * This method prepares the specified expression as a wildcard.
		 *
		 * @access public
		 * @override
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public function prepare_wildcard($expr) {
			if ( ! is_string($expr)) {
				throw new \Leap\Core\Throwable\InvalidArgument\Exception('Message: Invalid wildcard token specified. Reason: Token must be a string.', array(':expr' => $expr));
			}
			$parts = explode('.', $expr);
			$count = count($parts);
			for ($i = 0; $i < $count; $i++) {
				$parts[$i] = (trim($parts[$i]) != '*')
					? static::_OPENING_QUOTE_CHARACTER_ . trim(preg_replace('/[^a-z0-9$_ ]/i', '', $parts[$i])) . static::_CLOSING_QUOTE_CHARACTER_
					: '*';
			}
			if (isset($parts[$count - 1]) AND ($parts[$count - 1] != '*')) {
				$parts[] = '*';
			}
			$expr = implode('.', $parts);
			return $expr;
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * This variable stores the compiler's XML config file.
		 *
		 * @access protected
		 * @static
		 * @var \Leap\Core\Data\Serialization\XML
		 */
		protected static $xml = NULL;

		/**
		 * This method checks whether the specified token is a reserved keyword.
		 *
		 * @access public
		 * @static
		 * @param string $token                                     the token to be cross-referenced
		 * @return boolean                                          whether the token is a reserved keyword
		 *
		 * @see http://dev.mysql.com/doc/refman/5.6/en/reserved-words.html
		 */
		public static function is_keyword($token) {
			if (static::$xml === NULL) {
				static::$xml = \Leap\Core\Data\Serialization\XML::load('config/sql/mysql.xml');
			}
			$token = strtoupper($token);
			$nodes = static::$xml->xpath("/sql/dialect[@name='mysql' and @version='5.6']/keywords[keyword = '{$token}']");
			return ! empty($nodes);
		}

	}

}