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

namespace Leap\Plugin\DB\MsSQL\Connection {

	/**
	 * This class handles an improved MS SQL connection.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\MsSQL\Connection
	 * @version 2015-08-31
	 *
	 * @see http://php.net/manual/en/ref.sqlsrv.php
	 * @see http://blogs.msdn.com/b/brian_swan/archive/2010/03/08/mssql-vs-sqlsrv-what-s-the-difference-part-1.aspx
	 * @see http://blogs.msdn.com/b/brian_swan/archive/2010/03/10/mssql-vs-sqlsrv-what-s-the-difference-part-2.aspx
	 */
	class Improved extends \Leap\Core\DB\SQL\Connection\Standard {

		/**
		 * This destructor ensures that the connection is closed.
		 *
		 * @access public
		 * @override
		 */
		public function __destruct() {
			if (is_resource($this->resource)) {
				@sqlsrv_close($this->resource);
			}
			parent::__destruct();
		}

		/**
		 * This method begins a transaction.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 *
		 * @see http://msdn.microsoft.com/en-us/library/ms188929.aspx
		 * @see http://php.net/manual/en/function.sqlsrv-begin-transaction.php
		 */
		public function begin_transaction() {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to begin SQL transaction. Reason: Unable to find connection.');
			}
			$handle = @sqlsrv_begin_transaction($this->resource);
			if ($handle === FALSE) {
				$errors = @sqlsrv_errors(SQLSRV_ERR_ALL);
				$reason = (is_array($errors) AND isset($errors[0]['message']))
					? $errors[0]['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to begin the transaction. Reason: :reason', array(':reason' => $reason));
			}
			$this->command = new \Leap\Core\DB\SQL\Command('BEGIN TRAN;');
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
				if ( ! @sqlsrv_close($this->resource)) {
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
		 * @see http://msdn.microsoft.com/en-us/library/ms190295.aspx
		 * @see http://php.net/manual/en/function.sqlsrv-commit.php
		 */
		public function commit() {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to rollback SQL transaction. Reason: Unable to find connection.');
			}
			$handle = @sqlsrv_commit($this->resource);
			if ($handle === FALSE) {
				$errors = @sqlsrv_errors(SQLSRV_ERR_ALL);
				$reason = (is_array($errors) AND isset($errors[0]['message']))
					? $errors[0]['message']
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
		 *
		 * @see http://php.net/manual/en/function.sqlsrv-query.php
		 * @see http://php.net/manual/en/function.sqlsrv-free-stmt.php
		 */
		public function execute(\Leap\Core\DB\SQL\Command $command) {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL command. Reason: Unable to find connection.');
			}
			$handle = @sqlsrv_query($this->resource, $command->text);
			if ($handle === FALSE) {
				$errors = @sqlsrv_errors(SQLSRV_ERR_ALL);
				$reason = (is_array($errors) AND isset($errors[0]['message']))
					? $errors[0]['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL command. Reason: :reason', array(':reason' => $reason));
			}
			@sqlsrv_free_stmt($handle);
			$this->command = $command;
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
			try {
				if (is_string($table)) {
					$command = $this->command;
					$precompiler = \Leap\Core\DB\SQL::precompiler($this->data_source);
					$table = $precompiler->prepare_identifier($table);
					$column = $precompiler->prepare_identifier($column);
					$id = (int) $this->query(new \Leap\Core\DB\SQL\Command("SELECT MAX({$column}) AS [id] FROM {$table};"))->get('id', 0);
					$this->command = $command;
					return $id;
				}
				else {
					$command = $this->command;
					if (preg_match('/^INSERT\s+(TOP.+\s+)?INTO\s+(.*?)\s+/i', $command->text, $matches)) {
						$table = isset($matches[2]) ? $matches[2] : NULL;
						$query = ( ! empty($table)) ? "SELECT IDENT_CURRENT('{$table}') AS [id];" : 'SELECT SCOPE_IDENTITY() AS [id];';
						$id = (int) $this->query(new \Leap\Core\DB\SQL\Command($query))->get('id', 0);
						$this->command = $command;
						return $id;
					}
					return 0;
				}
			}
			catch (\Exception $ex) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to fetch the last insert id. Reason: :reason', array(':reason' => $ex->getMessage()));
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
		 * @see http://php.net/manual/en/function.sqlsrv-connect.php
		 * @see http://msdn.microsoft.com/en-us/library/cc644930.aspx
		 */
		public function open() {
			if ( ! $this->is_connected()) {
				$connection_string = $this->data_source->hostname;
				$port = $this->data_source->port;
				if ( ! empty($port)) {
					$connection_string .= ':' . $port;
				}

				$configurations = array();

				$configurations['Database'] = $this->data_source->database;
				$configurations['UID'] = $this->data_source->username;
				$configurations['PWD'] = $this->data_source->password;

				if ( ! empty($this->data_source->charset)) {
					$configurations['CharacterSet'] = $this->data_source->charset;
				}

				if ( ! $this->data_source->is_persistent()) {
					$configurations['ConnectionPooling'] = FALSE;
				}

				$this->resource = @sqlsrv_connect($connection_string, $configurations);

				if ($this->resource === FALSE) {
					$errors = @sqlsrv_errors(SQLSRV_ERR_ALL);
					$reason = (is_array($errors) AND isset($errors[0]['message']))
						? $errors[0]['message']
						: 'Unable to connect using the specified configurations.';
					throw new \Leap\Core\Throwable\Database\Exception('Message: Failed to establish connection. Reason: :reason', array(':reason' => $reason));
				}
			}
		}

		/**
		 * This method rollbacks a transaction.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 *
		 * @see http://php.net/manual/en/function.sqlsrv-rollback.php
		 */
		public function rollback() {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to rollback SQL transaction. Reason: Unable to find connection.');
			}
			$handle = @sqlsrv_rollback($this->resource);
			if ($handle === FALSE) {
				$errors = @sqlsrv_errors(SQLSRV_ERR_ALL);
				$reason = (is_array($errors) AND isset($errors[0]['message']))
					? $errors[0]['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to rollback SQL transaction. Reason: :reason', array(':reason' => $reason));
			}
			$this->command = new \Leap\Core\DB\SQL\Command('ROLLBACK;');
		}

	}

}