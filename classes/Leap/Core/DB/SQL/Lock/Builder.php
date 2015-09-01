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

namespace Leap\Core\DB\SQL\Lock {

	/**
	 * This class builds an SQL lock statement.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Lock
	 * @version 2015-08-23
	 */
	abstract class Builder extends \Leap\Core\Object {

		/**
		 * This variable stores a reference to the database connection.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\Connection\Driver
		 */
		protected $connection;

		/**
		 * This variable stores the build data for the SQL command.
		 *
		 * @access protected
		 * @var array
		 */
		protected $data;

		/**
		 * This variable stores a reference to the pre-compiler.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\SQL\Precompiler
		 */
		protected $precompiler;

		/**
		 * This constructor instantiates this class using the specified data source.
		 *
		 * @access public
		 * @param \Leap\Core\DB\Connection\Driver $connection       the connection to be used
		 */
		public function __construct(\Leap\Core\DB\Connection\Driver $connection) {
			$this->connection = $connection;
			$this->precompiler = \Leap\Core\DB\SQL::precompiler($connection->data_source);
			$this->reset();
		}

		/**
		 * This method releases any internal references to an object.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->connection);
			unset($this->data);
			unset($this->precompiler);
		}

		/**
		 * This method acquires the required locks.
		 *
		 * @access public
		 * @abstract
		 * @return \Leap\Core\DB\SQL\Lock\Builder                   a reference to the current instance
		 */
		public abstract function acquire();

		/**
		 * This method adds a lock definition.
		 *
		 * @access public
		 * @abstract
		 * @param string $table                                     the table to be locked
		 * @param array $hints                                      the hints to be applied
		 * @return \Leap\Core\DB\SQL\Lock\Builder                   a reference to the current instance
		 */
		public abstract function add($table, Array $hints = NULL);

		/**
		 * This method releases all acquired locks.
		 *
		 * @access public
		 * @abstract
		 * @param string $method                                    the method to be used to release
		 *                                                          the lock(s)
		 * @return \Leap\Core\DB\SQL\Lock\Builder                   a reference to the current instance
		 */
		public abstract function release($method = '');

		/**
		 * This method resets the current builder.
		 *
		 * @access public
		 * @return \Leap\Core\DB\SQL\Lock\Builder                   a reference to the current instance
		 */
		public function reset() {
			$this->data = array();
			return $this;
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * This method returns an instance of the appropriate SQL lock builder.
		 *
		 * @access public
		 * @static
		 * @param \Leap\Core\DB\Connection\Driver $connection       the connection to be used
		 * @return \Leap\Core\DB\SQL\Lock\Builder                   an instance of the appropriate
		 *                                                          SQL lock builder
		 */
		public static function factory(\Leap\Core\DB\Connection\Driver $connection) {
			$data_type = '\\Leap\\Plugin\\DB\\' . $connection->data_source->dialect . '\\Lock\\Builder';
			$builder = new $data_type($connection);
			return $builder;
		}

	}

}