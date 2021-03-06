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

namespace Leap\Core\DB\SQL\Connection {

	/**
	 * This class handles a PDO connection.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Connection
	 * @version 2015-08-23
	 *
	 * @see http://www.php.net/manual/en/book.pdo.php
	 * @see http://www.electrictoolbox.com/php-pdo-dsn-connection-string/
	 */
	abstract class PDO extends \Leap\Core\DB\Connection\Driver {

		/**
		 * This method begins a transaction.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 *
		 * @see http://www.php.net/manual/en/pdo.begintransaction.php
		 */
		public function begin_transaction() {
			try {
				$this->resource->beginTransaction();
				$this->command = new \Leap\Core\DB\SQL\Command('BEGIN TRANSACTION;');
			}
			catch (\Exception $ex) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to begin SQL transaction. Reason: :reason', array(':reason' => $ex->getMessage()));
			}
		}
		/**
		 * This method allows for the ability to close the connection that was opened.
		 *
		 * @access public
		 * @override
		 * @return boolean                                          whether an open connection was closed
		 */
		public function close() {
			if ($this->is_connected()) {
				unset($this->resource);
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
		 * @see http://www.php.net/manual/en/pdo.commit.php
		 */
		public function commit() {
			try {
				$this->resource->commit();
				$this->command = new \Leap\Core\DB\SQL\Command('COMMIT;');
			}
			catch (\Exception $ex) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to commit SQL transaction. Reason: :reason', array(':reason' => $ex->getMessage()));
			}
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
			$handle = @$this->resource->exec($command->text);
			if ($handle === FALSE) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL command. Reason: :reason', array(':reason' => $this->resource->errorInfo()));
			}
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
		 *
		 * @see http://www.php.net/manual/en/pdo.lastinsertid.php
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
					$alias = $precompiler->prepare_alias('id');
					$id = (int) $this->query(new \Leap\Core\DB\SQL\Command("SELECT MAX({$column}) AS {$alias} FROM {$table};"))->get('id', 0);
					$this->command = $command;
					return $id;
				}
				return $this->resource->lastInsertId();
			}
			catch (\Exception $ex) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to fetch the last insert id. Reason: :reason', array(':reason' => $ex->getMessage()));
			}
		}

		/**
		 * This method is for determining whether a connection is established.
		 *
		 * @access public
		 * @override
		 * @return boolean                                          whether a connection is established
		 */
		public function is_connected() {
			return ! empty($this->resource);
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

			$value = @$this->resource->quote($string);

			if ( ! is_string($value)) { // check needed since ODBC does not support quoting
				return parent::quote($string, $escape);
			}

			if (is_string($escape) OR ! empty($escape)) {
				$value .= " ESCAPE '{$escape}'";
			}

			return $value;
		}

		/**
		 * This method rollbacks a transaction.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 *
		 * @see http://www.php.net/manual/en/pdo.rollback.php
		 */
		public function rollback() {
			try {
				$this->resource->rollBack();
				$this->command = new \Leap\Core\DB\SQL\Command('ROLLBACK;');
			}
			catch (\Exception $ex) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to rollback SQL transaction. Reason: :reason', array(':reason' => $ex->getMessage()));
			}
		}

	}

}