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

namespace Leap\Core\DB\SQL {

	/**
	 * This class provides a set of functions for preparing SQL expressions.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL
	 * @version 2015-08-23
	 *
	 * @see http://en.wikibooks.org/wiki/SQL_Dialects_Reference
	 */
	abstract class Precompiler extends \Leap\Core\Object {

		/**
		 * This variable stores a reference to the data source.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\DataSource
		 */
		protected $data_source;

		/**
		 * This method initializes the class with the specified data source.
		 *
		 * @access public
		 * @param \Leap\Core\DB\DataSource $data_source             the data source to be used
		 */
		public function __construct(\Leap\Core\DB\DataSource $data_source) {
			$this->data_source = $data_source;
		}

		/**
		 * This method releases any internal references to an object.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->data_source);
		}

		/**
		 * This method prepares the specified expression as an alias.
		 *
		 * @access public
		 * @abstract
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public abstract function prepare_alias($expr);

		/**
		 * This method prepares the specified expression as a boolean.
		 *
		 * @access public
		 * @param mixed $expr                                       the expression to be prepared
		 * @return boolean                                          the prepared boolean value
		 */
		public function prepare_boolean($expr) {
			return (bool) $expr;
		}

		/**
		 * This method prepares the specified expression as a connector.
		 *
		 * @access public
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public function prepare_connector($expr) {
			if (is_string($expr)) {
				$expr = strtoupper($expr);
				switch ($expr) {
					case \Leap\Core\DB\SQL\Connector::_AND_:
					case \Leap\Core\DB\SQL\Connector::_OR_:
						return $expr;
					break;
				}
			}
			throw new \Leap\Core\Throwable\InvalidArgument\Exception('Message: Invalid connector token specified. Reason: Token must exist in the enumerated set.', array(':expr' => $expr));
		}

		/**
		 * This method prepares the specified expression as an identifier column.
		 *
		 * @access public
		 * @abstract
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public abstract function prepare_identifier($expr);

		/**
		 * This method prepares the specified expression as a join type.
		 *
		 * @access public
		 * @abstract
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public abstract function prepare_join($expr);

		/**
		 * This method prepares the specified expression as a natural number.
		 *
		 * @access public
		 * @param mixed $expr                                       the expression to be prepared
		 * @return integer                                          the prepared natural
		 */
		public function prepare_natural($expr) {
			return (is_numeric($expr)) ? (int) abs($expr) : 0;
		}

		/**
		 * This method prepares the specified expression as a operator.
		 *
		 * @access public
		 * @abstract
		 * @param string $expr                                      the expression to be prepared
		 * @param string $group                                     the operator grouping
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public abstract function prepare_operator($expr, $group);

		/**
		 * This method prepare the specified expression as a ordering token.
		 *
		 * @access public
		 * @abstract
		 * @param string $column                                    the column to be sorted
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          column will sorted either in ascending or
		 *                                                          descending order
		 * @param string $nulls                                     the weight to be given to null values
		 * @return string                                           the prepared clause
		 */
		public abstract function prepare_ordering($column, $ordering, $nulls);

		/**
		 * This method prepares the specified expression as a parenthesis.
		 *
		 * @access public
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public function prepare_parenthesis($expr) {
			if (is_string($expr)) {
				switch ($expr) {
					case \Leap\Core\DB\SQL\Builder::_OPENING_PARENTHESIS_:
					case \Leap\Core\DB\SQL\Builder::_CLOSING_PARENTHESIS_:
						return $expr;
					break;
				}
			}
			throw new \Leap\Core\Throwable\InvalidArgument\Exception('Message: Invalid parenthesis token specified. Reason: Token must exist in the enumerated set.', array(':expr' => $expr));
		}

		/**
		 * This method prepares the specified expression as a value.
		 *
		 * @access public
		 * @abstract
		 * @param string $expr                                      the expression to be prepared
		 * @param char $escape                                      the escape character
		 * @return string                                           the prepared expression
		 */
		public abstract function prepare_value($expr, $escape = NULL);

		/**
		 * This method prepares the specified expression as a wildcard.
		 *
		 * @access public
		 * @abstract
		 * @param string $expr                                      the expression to be prepared
		 * @return string                                           the prepared expression
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates a data type mismatch
		 */
		public abstract function prepare_wildcard($expr);

	}

}