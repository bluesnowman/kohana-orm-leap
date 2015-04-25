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

namespace Leap\Plugin\DB\Drizzle\Connection {

	/**
	 * This class handles a TCP Drizzle connection.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\Drizzle\Connection
	 * @version 2014-07-04
	 *
	 * @see http://devzone.zend.com/1504/getting-started-with-drizzle-and-php/
	 * @see https://github.com/barce/partition_benchmarks/blob/master/db.php
	 * @see http://plugins.svn.wordpress.org/drizzle/trunk/db.php
	 * @see http://ronaldbradford.com/blog/a-beginners-look-at-drizzle-datatypes-and-tables-2009-04-01/
	 */
	class TCP extends \Leap\Core\DB\SQL\Connection\Standard {

		/**
		 * This variable stores the last insert id.
		 *
		 * @access protected
		 * @var integer
		 */
		protected $id = FALSE;

		/**
		 * This destructor ensures that the connection is closed.
		 *
		 * @access public
		 * @override
		 */
		public function __destruct() {
			if (is_resource($this->resource)) {
				@drizzle_con_close($this->resource);
			}
		}

		/**
		 * This method begins a transaction.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 *
		 * @see http://docs.drizzle.org/start_transaction.html
		 */
		public function begin_transaction() {
			$this->execute(new \Leap\Core\DB\SQL\Command('START TRANSACTION;'));
		}

		/**
		 * This method closes an open connection.
		 *
		 * @access public
		 * @override
		 * @return boolean                                          whether an open connection was closed
		 */
		public function close() {
			if ($this->is_connected()) {
				if ( ! @drizzle_con_close($this->resource)) {
					return FALSE;
				}
				$this->resource = NULL;
			}
			return TRUE;
		}

		/**
		 * This method commits a transaction.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 *
		 * @see http://docs.drizzle.org/commit.html
		 */
		public function commit() {
			$this->execute(new \Leap\Core\DB\SQL\Command('COMMIT;'));
		}

		/**
		 * This method processes an SQL command that will NOT return data.
		 *
		 * @access public
		 * @override
		 * @param \Leap\Core\DB\SQL\Command $command                the SQL command to be queried
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 */
		public function execute(\Leap\Core\DB\SQL\Command $command) {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL command. Reason: Unable to find connection.');
			}
			$handle = @drizzle_query($this->resource, $command->text);
			if ($handle === FALSE) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL command. Reason: :reason', array(':reason' => @drizzle_con_error($this->resource)));
			}
			$this->insert_id = (preg_match("/^\\s*(insert|replace)\\s+/i", $command->text))
				? @drizzle_result_insert_id($handle)
				: FALSE;
			$this->command = $command;
			@drizzle_result_free($handle);
		}

		/**
		 * This method returns the last insert id.
		 *
		 * @access public
		 * @override
		 * @param string $table                                     the table to be queried
		 * @param string $column                                    the column representing the table's id
		 * @return integer                                          the last insert id
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the query failed
		 */
		public function get_last_insert_id($table = NULL, $column = 'id') {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to fetch the last insert id. Reason: Unable to find connection.');
			}
			if (is_string($table)) {
				$command = $this->command;
				$precompiler = \Leap\Core\DB\SQL::precompiler($this->data_source);
				$table = $precompiler->prepare_identifier($table);
				$column = $precompiler->prepare_identifier($column);
				$id = (int) $this->query(new \Leap\Core\DB\SQL\Command("SELECT MAX({$column}) AS `id` FROM {$table};"))->get('id', 0);
				$this->command = $command;
				return $id;
			}
			else {
				if ($this->insert_id === FALSE) {
					throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to fetch the last insert id. Reason: No insert id could be derived.');
				}
				return $this->insert_id;
			}
		}

		/**
		 * This method opens a connection using the data source provided.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\Database\Exception          indicates that there is problem with
		 *                                                          opening the connection
		 *
		 * @see http://wiki.drizzle.org/MySQL_Differences
		 */
		public function open() {
			if ( ! $this->is_connected()) {
				$handle = drizzle_create();
				$hostname = $this->data_source->hostname;
				$port = $this->data_source->port;
				$database = $this->data_source->database;
				$username = $this->data_source->username;
				$password = $this->data_source->password;
				$this->resource = @drizzle_con_add_tcp($handle, $hostname, $port, $username, $password, $database, 0);
				if ($this->resource === FALSE) {
					throw new \Leap\Core\Throwable\Database\Exception('Message: Failed to establish connection. Reason: :reason', array(':reason' => @drizzle_error($handle)));
				}
				// "There is no CHARSET or CHARACTER SET commands, everything defaults to UTF-8."
			}
		}

		/**
		 * This method processes an SQL command that will return data.
		 *
		 * @access public
		 * @override
		 * @param \Leap\Core\DB\SQL\Command $command                the SQL command to be queried
		 * @param string $type                                      the return type to be used
		 * @return \Leap\Core\DB\ResultSet                          the result set
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the query failed
		 */
		public function query(\Leap\Core\DB\SQL\Command $command, $type = 'array') {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to query SQL command. Reason: Unable to find connection.');
			}
			$result_set = $this->cache($command, $type);
			if ($result_set !== NULL) {
				$this->insert_id = FALSE;
				$this->command = $command;
				return $result_set;
			}
			$reader = \Leap\Core\DB\SQL\DataReader::factory($this, $command);
			$result_set = $this->cache($command, $type, new \Leap\Core\DB\ResultSet($reader, $type));
			$this->insert_id = FALSE;
			$this->command = $command;
			return $result_set;
		}

		/**
		 * This method escapes a string to be used in an SQL command.
		 *
		 * @access public
		 * @override
		 * @param string $string                                    the string to be escaped
		 * @param char $escape                                      the escape character
		 * @return string                                           the quoted string
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that no connection could
		 *                                                          be found
		 */
		public function quote($string, $escape = NULL) {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to quote/escape string. Reason: Unable to find connection.');
			}

			$string = "'" . drizzle_escape_string($this->resource, $string) . "'";

			if (is_string($escape) OR ! empty($escape)) {
				$string .= " ESCAPE '{$escape}'";
			}

			return $string;
		}

		/**
		 * This method rollbacks a transaction.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 *
		 * @see http://docs.drizzle.org/rollback.html
		 */
		public function rollback() {
			$this->execute(new \Leap\Core\DB\SQL\Command('ROLLBACK;'));
		}

	}

}