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

namespace Leap\Core\DB\SQL\Update {

	/**
	 * This class builds an SQL update statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Update
	 * @version 2014-07-04
	 */
	class Proxy extends \Leap\Core\Object implements \Leap\Core\DB\SQL\Statement {

		/**
		 * This variable stores the delegate that will be called after a connection
		 * operation.
		 *
		 * @access protected
		 * @var callable
		 */
		protected $after;

		/**
		 * This variable stores the delegate that will be called before a connection
		 * operation.
		 *
		 * @access protected
		 * @var callable
		 */
		protected $before;

		/**
		 * This variable stores an instance of the SQL builder class.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\SQL\Update\Builder
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
		 * This method sets the delegate that will be called after the connection
		 * operation.
		 *
		 * @access public
		 * @unsafe
		 * @param callable $delegate
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
		 */
		public function after(callable $delegate) {
			$this->after = $delegate;
			return $this;
		}

		/**
		 * This method sets the delegate that will be called before the connection
		 * operation.
		 *
		 * @access public
		 * @unsafe
		 * @param callable $delegate
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
		 */
		public function before(callable $delegate) {
			$this->before = $delegate;
			return $this;
		}

		/**
		 * This constructor instantiates this class using the specified data source.
		 *
		 * @access public
		 * @param \Leap\Core\DB\DataSource $data_source             the data source to be used
		 */
		public function __construct(\Leap\Core\DB\DataSource $data_source) {
			$data_type = '\\Leap\\Plugin\\DB\\' . $data_source->dialect . '\\Update\\Builder';
			$this->builder = new $data_type($data_source);
			$this->data_source = $data_source;
		}

		/**
		 * This method returns the raw SQL command.
		 *
		 * @access public
		 * @override
		 * @return string                                           the raw SQL command
		 */
		public function __toString() {
			return $this->builder->command()->__toString();
		}

		/**
		 * This method executes the built SQL command.
		 *
		 * @access public
		 */
		public function execute() {
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);
			if ($this->before !== NULL) {
				call_user_func_array($this->before, array($connection));
			}
			$connection->execute($this->command());
			if ($this->after !== NULL) {
				call_user_func_array($this->after, array($connection));
			}
		}

		/**
		 * This method sets a "limit" constraint on the statement.
		 *
		 * @access public
		 * @param integer $limit                                    the "limit" constraint
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
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
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
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
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
		 */
		public function order_by($column, $ordering = 'ASC', $nulls = 'DEFAULT') {
			$this->builder->order_by($column, $ordering, $nulls);
			return $this;
		}

		/**
		 * This method resets the current builder.
		 *
		 * @access public
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
		 */
		public function reset() {
			$this->builder->reset();
			return $this;
		}

		/**
		 * This method sets the associated value with the specified column.
		 *
		 * @access public
		 * @param string $column                                    the column to be set
		 * @param string $value                                     the value to be set
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
		 */
		public function set($column, $value) {
			$this->builder->set($column, $value);
			return $this;
		}

		/**
		 * This method returns the SQL command.
		 *
		 * @access public
		 * @override
		 * @param boolean $terminated                               whether to add a semi-colon to the end
		 *                                                          of the statement
		 * @return \Leap\Core\DB\SQL\Command                        the SQL command
		 */
		public function command($terminated = TRUE) {
			return $this->builder->command($terminated);
		}

		/**
		 * This method sets which table will be modified.
		 *
		 * @access public
		 * @param string $table                                     the database table to be modified
		 * @param string $alias                                     the alias to be used for the specified table
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
		 */
		public function table($table, $alias = NULL) {
			$this->builder->table($table, $alias);
			return $this;
		}

		/**
		 * This method adds a "where" constraint.
		 *
		 * @access public
		 * @param string $column                                    the column to be constrained
		 * @param string $operator                                  the operator to be used
		 * @param string $value                                     the value the column is constrained with
		 * @param string $connector                                 the connector to be used
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
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
		 * @return \Leap\Core\DB\SQL\Update\Proxy                   a reference to the current instance
		 */
		public function where_block($parenthesis, $connector = 'AND') {
			$this->builder->where_block($parenthesis, $connector);
			return $this;
		}

	}

}