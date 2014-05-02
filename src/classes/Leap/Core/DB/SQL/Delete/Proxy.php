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

namespace Leap\Core\DB\SQL\Delete {

	/**
	 * This class builds an SQL delete statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Delete
	 * @version 2014-05-01
	 */
	class Proxy extends \Leap\Core\Object implements \Leap\Core\DB\SQL\Statement {

		/**
		 * This variable stores an instance of the SQL builder class.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\SQL\Delete\Builder
		 */
		protected $builder;

		/**
		 * This variable stores a reference to the data source.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\DataSource
		 */
		protected $data_source;

		/**
		 * This constructor instantiates this class using the specified data source.
		 *
		 * @access public
		 * @param mixed $config                                     the data source configurations
		 */
		public function __construct($config) {
			$this->data_source = \Leap\Core\DB\DataSource::instance($config);
			$builder = '\\Leap\\Plugin\\DB\\' . $this->data_source->dialect . '\\Delete\\Builder';
			$this->builder = new $builder($this->data_source);
		}

		/**
		 * This method returns the raw SQL statement.
		 *
		 * @access public
		 * @override
		 * @return string                                           the raw SQL statement
		 */
		public function __toString() {
			return $this->builder->statement()->__toString();
		}

		/**
		 * This method executes the built SQL statement.
		 *
		 * @access public
		 */
		public function execute() {
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);
			$connection->execute($this->statement());
		}

		/**
		 * This method sets which table will be modified.
		 *
		 * @access public
		 * @param string $table                                     the database table to be modified
		 * @return \Leap\Core\DB\SQL\Delete\Proxy                   a reference to the current instance
		 */
		public function from($table) {
			$this->builder->from($table);
			return $this;
		}

		/**
		 * This method sets a "limit" constraint on the statement.
		 *
		 * @access public
		 * @param integer $limit                                    the "limit" constraint
		 * @return \Leap\Core\DB\SQL\Delete\Proxy                   a reference to the current instance
		 */
		public function limit($limit) {
			$this->builder->limit($limit);
			return $this;
		}

		/**
		 * This method sets an "offset" constraint on the statement.
		 *
		 * @access public
		 * @param integer $offset                                   the "offset" constraint
		 * @return \Leap\Core\DB\SQL\Delete\Proxy                   a reference to the current instance
		 */
		public function offset($offset) {
			$this->builder->offset($offset);
			return $this;
		}

		/**
		 * This method sets how a column will be sorted.
		 *
		 * @access public
		 * @param string $column                                    the column to be sorted
		 * @param string $ordering                                  the ordering token that signals whether the
		 *                                                          column will sorted either in ascending or
		 *                                                          descending order
		 * @param string $nulls                                     the weight to be given to null values
		 * @return \Leap\Core\DB\SQL\Delete\Proxy                   a reference to the current instance
		 */
		public function order_by($column, $ordering = 'ASC', $nulls = 'DEFAULT') {
			$this->builder->order_by($column, $ordering, $nulls);
			return $this;
		}

		/**
		 * This method resets the current builder.
		 *
		 * @access public
		 * @return \Leap\Core\DB\SQL\Delete\Proxy                   a reference to the current instance
		 */
		public function reset() {
			$this->builder->reset();
			return $this;
		}

		/**
		 * This method returns the SQL statement.
		 *
		 * @access public
		 * @override
		 * @param boolean $terminated                               whether to add a semi-colon to the end
		 *                                                          of the statement
		 * @return \Leap\Core\DB\SQL\Command                        the SQL statement
		 */
		public function statement($terminated = TRUE) {
			return $this->builder->statement($terminated);
		}

		/**
		 * This method adds a "where" constraint.
		 *
		 * @access public
		 * @param string $column                                    the column to be constrained
		 * @param string $operator                                  the operator to be used
		 * @param string $value                                     the value the column is constrained with
		 * @param string $connector                                 the connector to be used
		 * @return \Leap\Core\DB\SQL\Delete\Proxy                   a reference to the current instance
		 */
		public function where($column, $operator, $value, $connector = 'AND') {
			$this->builder->where($column, $operator, $value, $connector);
			return $this;
		}

		/**
		 * This method either opens or closes a "where" group.
		 *
		 * @access public
		 * @param string $parenthesis                               the parenthesis to be used
		 * @param string $connector                                 the connector to be used
		 * @return \Leap\Core\DB\SQL\Delete\Proxy                   a reference to the current instance
		 */
		public function where_block($parenthesis, $connector = 'AND') {
			$this->builder->where_block($parenthesis, $connector);
			return $this;
		}

	}

}