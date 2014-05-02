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

namespace Leap\Core\DB\ORM\Select {

	/**
	 * This class builds an SQL select statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM\Select
	 * @version 2014-05-01
	 */
	class Proxy extends \Leap\Core\Object implements \Leap\Core\DB\SQL\Statement {

		/**
		 * This variable stores an instance of the SQL builder class.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\SQL\Select\Builder
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
		 * This variable stores an instance of the ORM builder extension class.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\ORM\Builder
		 */
		protected $extension;

		/**
		 * This variable stores the model's name.
		 *
		 * @access protected
		 * @var string
		 */
		protected $model;

		/**
		 * This variable stores the name of the model's table.
		 *
		 * @access protected
		 * @var string
		 */
		protected $table = NULL;

		/**
		 * This method attempts to call an otherwise inaccessible function on the model's
		 * builder extension.
		 *
		 * @access public
		 * @override
		 * @param string $function                                  the name of the called function
		 * @param array $arguments                                  an array with the parameters passed
		 * @return mixed                                            the result of the called function
		 * @throws \Leap\Core\Throwable\UnimplementedMethod\Exception  indicates that the called function is
		 *                                                          inaccessible
		 */
		public function __call($function, $arguments) {
			if ($this->extension !== NULL) {
				if (method_exists($this->extension, $function)) {
					$result = call_user_func_array(array($this->extension, $function), $arguments);
					if ($result instanceof \Leap\Core\DB\ORM\Builder) {
						return $this;
					}
					return $result;
				}
			}
			throw new \Leap\Core\Throwable\UnimplementedMethod\Exception('Message: Call to undefined member function. Reason: Function :function has not been defined in class :class.', array(':class' => get_class($this->extension), ':function' => $function, ':arguments' => $arguments));
		}

		/**
		 * This constructor instantiates this class using the specified model's name.
		 *
		 * @access public
		 * @param string $model                                     the model's name
		 * @param array $columns                                    the columns to be selected
		 */
		public function __construct($model, Array $columns = array()) {
			$name = $model;
			$model = \Leap\Core\DB\ORM\Model::model_name($name);
			$this->data_source = \Leap\Core\DB\DataSource::instance($model::data_source(\Leap\Core\DB\DataSource::SLAVE_INSTANCE));
			$builder = '\\Leap\\Plugin\\DB\\' . $this->data_source->dialect . '\\Select\\Builder';
			$this->table = $model::table();
			$this->builder = new $builder($this->data_source, $columns);
			if (empty($columns)) {
				$this->builder->all("{$this->table}.*");
			}
			$this->builder->from($this->table);
			$extension = \Leap\Core\DB\ORM\Model::builder_name($name);
			if (class_exists($extension)) {
				$this->extension = new $extension($this->builder);
			}
			$this->model = $model;
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
		 * This method sets the wildcard to be used.
		 *
		 * @access public
		 * @param string $wildcard                                  the wildcard to be used
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function all($wildcard = '*') {
			$this->builder->all("{$this->table}.*");
			return $this;
		}

		/**
		 * This method explicits sets the specified column to be selected.
		 *
		 * @access public
		 * @param string $column                                    the column to be selected
		 * @param string $alias                                     the alias to be used for the specified column
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function column($column, $alias = NULL) {
			$this->builder->column($column, $alias);
			return $this;
		}

		/**
		 * This method combines another SQL statement using the specified operator.
		 *
		 * @access public
		 * @param string $operator                                  the operator to be used to append
		 *                                                          the specified SQL statement
		 * @param string $statement                                 the SQL statement to be appended
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function combine($operator, $statement) {
			$this->builder->combine($operator, $statement);
			return $this;
		}

		/**
		 * This method sets whether to constrain the SQL statement to only distinct records.
		 *
		 * @access public
		 * @param boolean $distinct                                 whether to constrain the SQL statement to only
		 *                                                          distinct records
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function distinct($distinct = TRUE) {
			$this->builder->distinct($distinct);
			return $this;
		}

		/**
		 * This method adds a "group by" clause.
		 *
		 * @access public
		 * @param string $column                                    the column to be grouped
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function group_by($column) {
			$this->builder->group_by($column);
			return $this;
		}

		/**
		 * This method adds a "having" constraint.
		 *
		 * @access public
		 * @param string $column                                    the column to be constrained
		 * @param string $operator                                  the operator to be used
		 * @param string $value                                     the value the column is constrained with
		 * @param string $connector                                 the connector to be used
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function having($column, $operator, $value, $connector = 'AND') {
			$this->builder->having($column, $operator, $value, $connector);
			return $this;
		}

		/**
		 * This method either opens or closes a "having" group.
		 *
		 * @access public
		 * @param string $parenthesis                               the parenthesis to be used
		 * @param string $connector                                 the connector to be used
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function having_block($parenthesis, $connector = 'AND') {
			$this->builder->having_block($parenthesis, $connector);
			return $this;
		}

		/**
		 * This method joins a table.
		 *
		 * @access public
		 * @param string $type                                      the type of join
		 * @param string $table                                     the table to be joined
		 * @param string $alias                                     the alias to be used for the specified table
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function join($type, $table, $alias = NULL) {
			$this->builder->join($type, $table, $alias);
			return $this;
		}

		/**
		 * This method sets a "limit" constraint on the statement.
		 *
		 * @access public
		 * @param integer $limit                                    the "limit" constraint
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
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
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function offset($offset) {
			$this->builder->offset($offset);
			return $this;
		}

		/**
		 * This method sets an "on" constraint for the last join specified.
		 *
		 * @access public
		 * @param string $column0                                   the column to be constrained on
		 * @param string $operator                                  the operator to be used
		 * @param string $column1                                   the constraint column
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates an invalid SQL build instruction
		 */
		public function on($column0, $operator, $column1) {
			$this->builder->on($column0, $operator, $column1);
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
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function order_by($column, $ordering = 'ASC', $nulls = 'DEFAULT') {
			$this->builder->order_by($column, $ordering, $nulls);
			return $this;
		}

		/**
		 * This method sets both the "offset" constraint and the "limit" constraint on
		 * the statement.
		 *
		 * @access public
		 * @param integer $offset                                   the "offset" constraint
		 * @param integer $limit                                    the "limit" constraint
		 * @return \Leap\Core\DB\SQL\Select\Builder                 a reference to the current instance
		 */
		public function page($offset, $limit) {
			$this->builder->page($offset, $limit);
			return $this;
		}

		/**
		 * This method performs a query using the built SQL statement.
		 *
		 * @access public
		 * @param integer $limit                                    the "limit" constraint
		 * @return \Leap\Core\DB\ResultSet                          the result set
		 */
		public function query($limit = NULL) {
			if ($limit !== NULL) {
				$this->limit($limit);
			}
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);
			$records = $connection->query($this->statement(), $this->model);
			return $records;
		}

		/**
		 * This method resets the current builder.
		 *
		 * @access public
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
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
		 * This method sets a "using" constraint for the last join specified.
		 *
		 * @access public
		 * @param string $column                                    the column to be constrained
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function using($column) {
			$this->builder->using($column);
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
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
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
		 * @return \Leap\Core\DB\ORM\Select\Proxy                   a reference to the current instance
		 */
		public function where_block($parenthesis, $connector = 'AND') {
			$this->builder->where_block($parenthesis, $connector);
			return $this;
		}

	}

}