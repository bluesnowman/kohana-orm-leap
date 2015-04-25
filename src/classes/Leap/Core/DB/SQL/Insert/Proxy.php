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

namespace Leap\Core\DB\SQL\Insert {

	/**
	 * This class builds an SQL insert statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Insert
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
		 * This variable stores an instance of the SQL command builder of the preferred SQL
		 * language dialect.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\SQL\Builder
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
		 * @return \Leap\Core\DB\SQL\Insert\Proxy                   a reference to the current instance
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
		 * @return \Leap\Core\DB\SQL\Insert\Proxy                   a reference to the current instance
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
			$data_type = '\\Leap\\Plugin\\DB\\' . $data_source->dialect . '\\Insert\\Builder';
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
		 * This method sets the associated value with the specified column.
		 *
		 * @access public
		 * @param string $column                 	                the column to be set
		 * @param string $value                  	                the value to be set
		 * @param integer $row						                the index of the row
		 * @return \Leap\Core\DB\SQL\Insert\Proxy                   a reference to the current instance
		 */
		public function column($column, $value, $row = 0) {
			$this->builder->column($column, $value, $row);
			return $this;
		}

		/**
		 * This method executes the SQL command via the DAO class.
		 *
		 * @access public
		 * @param boolean $auto_increment		  	                whether to query for the last insert id
		 * @return integer                      	                the last insert id
		 */
		public function execute() {
			$auto_increment = ((func_num_args() > 0) AND (func_get_arg(0) === TRUE));
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);
			if ($this->before !== NULL) {
				call_user_func_array($this->before, array($connection));
			}
			$connection->execute($this->command());
			$primary_key = ($auto_increment) ? $connection->get_last_insert_id() : 0;
			if ($this->after !== NULL) {
				call_user_func_array($this->after, array($connection));
			}
			return $primary_key;
		}

		/**
		 * This method sets which table will be modified.
		 *
		 * @access public
		 * @param string $table                                     the database table to be modified
		 * @return \Leap\Core\DB\SQL\Insert\Proxy                   a reference to the current instance
		 */
		public function into($table) {
			$this->builder->into($table);
			return $this;
		}

		/**
		 * This method resets the current builder.
		 *
		 * @access public
		 * @return \Leap\Core\DB\SQL\Insert\Proxy                   a reference to the current instance
		 */
		public function reset() {
			$this->builder->reset();
			return $this;
		}

		/**
		 * This method sets a row of columns/values pairs.
		 *
		 * @access public
		 * @param array $values						                the columns/values pairs to be set
		 * @param integer $row						                the index of the row
		 * @return \Leap\Core\DB\SQL\Insert\Proxy  			        a reference to the current instance
		 */
		public function row(Array $values, $row = 0) {
			$this->builder->row($values, $row);
			return $this;
		}

		/**
		 * This method returns the SQL command.
		 *
		 * @access public
		 * @override
		 * @param boolean $terminated           	                whether to add a semi-colon to the end
		 *                                      	                of the statement
		 * @return string                       	                the SQL command
		 */
		public function command($terminated = TRUE) {
			return $this->builder->command($terminated);
		}

	}

}