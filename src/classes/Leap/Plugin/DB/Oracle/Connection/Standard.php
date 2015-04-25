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

namespace Leap\Plugin\DB\Oracle\Connection {

	/**
	 * This class handles a standard Oracle connection.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\Oracle\Connection
	 * @version 2014-07-04
	 *
	 * @see http://php.net/manual/en/book.oci8.php
	 */
	class Standard extends \Leap\Core\DB\SQL\Connection\Standard {

		/**
		 * This variable stores the execution mode, which is used to handle transactions.
		 *
		 * @access protected
		 * @var integer
		 */
		protected $execution_mode;

		/**
		 * This destructor ensures that the connection is closed.
		 *
		 * @access public
		 * @override
		 *
		 * @see http://www.php.net/manual/en/function.oci-close.php
		 */
		public function __destruct() {
			if (is_resource($this->resource)) {
				@oci_close($this->resource);
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
		 * @see http://www.php.net/manual/en/function.oci-rollback.php
		 * @see http://www.php.net/manual/en/function.oci-commit.php
		 */
		public function begin_transaction() {
			if ( ! $this->is_connected()) {
				$this->execution_mode = OCI_COMMIT_ON_SUCCESS;
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to begin SQL transaction. Reason: Unable to find connection.');
			}
			$this->execution_mode = (PHP_VERSION_ID > 50301)
				? OCI_NO_AUTO_COMMIT // Use with PHP > 5.3.1
				: OCI_DEFAULT;       // Use with PHP <= 5.3.1
			$this->command = new \Leap\Core\DB\SQL\Command('BEGIN TRANSACTION;');
		}

		/**
		 * This method closes an open connection.
		 *
		 * @access public
		 * @override
		 * @return boolean                                          whether an open connection was closed
		 *
		 * @see http://www.php.net/manual/en/function.oci-close.php
		 */
		public function close() {
			if ($this->is_connected()) {
				if ( ! @oci_close($this->resource)) {
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
		 * @see http://www.php.net/manual/en/function.oci-commit.php
		 */
		public function commit() {
			$this->execution_mode = OCI_COMMIT_ON_SUCCESS;
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to commit SQL transaction. Reason: Unable to find connection.');
			}
			$handle = @oci_commit($this->resource);
			if ($handle === FALSE) {
				$error = @oci_error($this->resource);
				$reason = (is_array($error) AND isset($error['message']))
					? $error['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to commit SQL transaction. Reason: :reason', array(':reason' => $reason));
			}
			$this->command = new \Leap\Core\DB\SQL\Command('COMMIT;');
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
			$handle = @oci_parse($this->resource, \Leap\Core\DB\SQL\Command::trim($command->text));
			if ($handle === FALSE) {
				$error = @oci_error($this->resource);
				$reason = (is_array($error) AND isset($error['message']))
					? $error['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL command. Reason: :reason', array(':reason' => $reason));
			}
			if ( ! oci_execute($handle, $this->execution_mode)) {
				$error = @oci_error($handle);
				$reason = (is_array($error) AND isset($error['message']))
					? $error['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL command. Reason: :reason', array(':reason' => $reason));
			}
			$this->command = $command;
			@oci_free_command($handle);
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
		 *
		 * @see http://stackoverflow.com/questions/3131064/get-id-of-last-inserted-record-in-oracle-db
		 * @see http://stackoverflow.com/questions/3558433/php-oracle-take-the-autogenerated-id-after-an-insert
		 */
		public function get_last_insert_id($table = NULL, $column = 'id') {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to fetch the last insert id. Reason: Unable to find connection.');
			}
			try {
				if (is_string($table)) {
					$command = $this->command;
					$precompiler = \Leap\Core\DB\SQL::precompiler($this->data_source);
					$table = $precompiler->prepare_identifier($table);
					$column = $precompiler->prepare_identifier($column);
					$id = (int) $this->query(new \Leap\Core\DB\SQL\Command("SELECT MAX({$column}) AS \"id\" FROM {$table};"))->get('id', 0);
					$this->command = $command;
					return $id;
				}
				else {
					$command = $this->command;
					if (preg_match('/^INSERT\s+INTO\s+(.*?)\s+/i', $command->text, $matches)) {
						if (isset($matches[1])) {
							$table = $matches[1];
							$id = (int) $this->query(new \Leap\Core\DB\SQL\Command("SELECT MAX(ID) AS \"id\" FROM {$table};"))->get('id', 0);
							$this->command = $command;
							return $id;
						}
					}
					return 0;
				}
			}
			catch (\Exception $ex) {
				throw new \Leap\Core\Throwable\SQL\Exception(preg_replace('/Failed to query SQL command./', 'Failed to fetch the last insert id.', $ex->getMessage()));
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
		 * @see http://www.php.net/manual/en/function.oci-connect.php
		 * @see http://download.oracle.com/docs/cd/E11882_01/network.112/e10836/naming.htm
		 * @see http://docs.oracle.com/cd/B10501_01/server.920/a96529/ch2.htm#100150
		 */
		public function open() {
			if ( ! $this->is_connected()) {
				$hostname = $this->data_source->hostname;
				$database = $this->data_source->database;
				if ( ! empty($hostname) ) {
					$connection_string = '//'. $hostname;
					$port = $this->data_source->port; // default port is 1521
					if ( ! empty($port)) {
						$connection_string .= ':' . $port;
					}
					$connection_string .= '/' . $database;
				}
				else if (isset($database)) {
					$connection_string = $database;
				}
				else {
					throw new \Leap\Core\Throwable\Database\Exception('Message: Bad configuration. Reason: Data source needs to define either a //host[:port][/database] or a database name scheme.', array(':dsn' => $this->data_source->id));
				}
				$username = $this->data_source->username;
				$password = $this->data_source->password;
				if ( ! empty($this->data_source->charset)) {
					$charset = strtoupper($this->data_source->charset);
					$this->resource = ($this->data_source->is_persistent())
						? @oci_pconnect($username, $password, $connection_string, $charset)
						: @oci_connect($username, $password, $connection_string, $charset);
				}
				else {
					$this->resource = ($this->data_source->is_persistent())
						? @oci_pconnect($username, $password, $connection_string)
						: @oci_connect($username, $password, $connection_string);
				}
				if ($this->resource === FALSE) {
					$error = @oci_error();
					$reason = (is_array($error) AND isset($error['message']))
						? $error['message']
						: 'Unable to connect to database.';
					throw new \Leap\Core\Throwable\Database\Exception('Message: Failed to establish connection. Reason: :reason', array(':reason' => $reason));
				}
				$this->execution_mode = OCI_COMMIT_ON_SUCCESS;
			}
		}

		/**
		 * This method processes an SQL command that will return data.
		 *
		 * @access public
		 * @override
		 * @param \Leap\Core\DB\SQL\Command $command                the SQL command to be queried
		 * @param string $type						                the return type to be used
		 * @return \Leap\Core\DB\ResultSet                          the result set
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the query failed
		 */
		public function query(\Leap\Core\DB\SQL\Command $command, $type = 'array') {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to query SQL command. Reason: Unable to find connection.');
			}
			$result_set = $this->cache($command, $type);
			if ($result_set !== NULL) {
				$this->command = $command;
				return $result_set;
			}
			$reader = \Leap\Core\DB\SQL\DataReader::factory($this, $command, $this->execution_mode);
			$result_set = $this->cache($command, $type, new \Leap\Core\DB\ResultSet($reader, $type));
			$this->command = $command;
			return $result_set;
		}

		/**
		 * This method creates a data reader for query the specified SQL command.
		 *
		 * @access public
		 * @param \Leap\Core\DB\SQL\Command $command                the SQL command to be queried
		 * @return \Leap\Core\DB\SQL\DataReader                     the SQL data reader
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the query failed
		 */
		public function reader(\Leap\Core\DB\SQL\Command $command) {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to create SQL data reader. Reason: Unable to find connection.');
			}
			$reader = \Leap\Core\DB\SQL\DataReader::factory($this, $command, $this->execution_mode);
			$this->command = $command;
			return $reader;
		}

		/**
		 * This method rollbacks a transaction.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 *
		 * @see http://www.php.net/manual/en/function.oci-rollback.php
		 */
		public function rollback() {
			$this->execution_mode = OCI_COMMIT_ON_SUCCESS;
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to rollback SQL transaction. Reason: Unable to find connection.');
			}
			$handle = @oci_rollback($this->resource);
			if ($handle === FALSE) {
				$error = @oci_error($this->resource);
				$reason = (is_array($error) AND isset($error['message']))
					? $error['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to rollback SQL transaction. Reason: :reason', array(':reason' => $reason));
			}
			$this->command = new \Leap\Core\DB\SQL\Command('ROLLBACK;');
		}

	}

}