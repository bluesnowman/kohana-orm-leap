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

namespace Leap\Core\DB\ORM\Insert {

	/**
	 * This class builds an SQL insert statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM\Insert
	 * @version 2014-04-30
	 */
	class Proxy extends \Leap\Core\Object implements \Leap\Core\DB\SQL\Statement {

		/**
		 * This variable stores an instance of the SQL builder class.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\SQL\Insert\Builder
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
		 * This constructor instantiates this class using the specified model's name.
		 *
		 * @access public
		 * @param string $model                                     the model's name
		 */
		public function __construct($model) {
			$name = $model;
			$model = \Leap\Core\DB\ORM\Model::model_name($name);
			$this->data_source = \Leap\Core\DB\DataSource::instance($model::data_source(\Leap\Core\DB\DataSource::MASTER_INSTANCE));
			$builder = '\\Leap\\Plugin\\DB\\' . $this->data_source->dialect . '\\Insert\\Builder';
			$this->builder = new $builder($this->data_source);
			$extension = \Leap\Core\DB\ORM\Model::builder_name($name);
			if (class_exists($extension)) {
				$this->extension = new $extension($this->builder);
			}
			$table = $model::table();
			$this->builder->into($table);
			$this->model = $model;
		}

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
		 * This method sets the associated value with the specified column.
		 *
		 * @access public
		 * @param string $column                                    the column to be set
		 * @param string $value                                     the value to be set
		 * @return \Leap\Core\DB\ORM\Insert\Proxy                   a reference to the current instance
		 */
		public function column($column, $value) {
			$this->builder->column($column, $value, 0);
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
		 * This method returns the raw SQL statement.
		 *
		 * @access public
		 * @override
		 * @return string                                           the raw SQL statement
		 */
		public function __toString() {
			return $this->builder->statement(TRUE)->__toString();
		}

		/**
		 * This method executes the SQL statement.
		 *
		 * @access public
		 * @return integer                                          the last insert id
		 */
		public function execute() {
			$model = $this->model;
			$auto_increment = $model::is_auto_incremented();
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);
			$connection->execute($this->statement());
			$primary_key = ($auto_increment) ? $connection->get_last_insert_id() : 0;
			return $primary_key;
		}

		/**
		 * This method resets the current builder.
		 *
		 * @access public
		 * @return \Leap\Core\DB\ORM\Insert\Proxy                   a reference to the current instance
		 */
		public function reset() {
			$this->builder->reset();
			return $this;
		}

	}

}