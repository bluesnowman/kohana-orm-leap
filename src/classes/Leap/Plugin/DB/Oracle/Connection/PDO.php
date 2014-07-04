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

namespace Leap\Plugin\DB\Oracle\Connection {

	/**
	 * This class handles a PDO Oracle connection.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\Oracle\Connection
	 * @version 2014-07-03
	 *
	 * @see http://www.php.net/manual/en/ref.pdo-oci.php
	 */
	class PDO extends \Leap\Core\DB\SQL\Connection\PDO {

		/**
		 * This method processes an SQL statement that will NOT return data.
		 *
		 * @access public
		 * @override
		 * @param \Leap\Core\DB\SQL\Command $sql					the SQL statement
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the executed
		 *                                                          statement failed
		 */
		public function execute(\Leap\Core\DB\SQL\Command $sql) {
			if ( ! $this->is_connected()) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL statement. Reason: Unable to find connection.');
			}
			$command = @$this->resource->exec(\Leap\Core\DB\SQL\Command::trim($sql->text));
			if ($command === FALSE) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to execute SQL statement. Reason: :reason', array(':reason' => $this->resource->errorInfo()));
			}
			$this->sql = $sql;
		}

		/**
		 * This method opens a connection using the data source provided.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\Database\Exception          indicates that there is problem with
		 *                                                          opening the connection
		 *
		 * @see http://www.php.net/manual/en/ref.pdo-oci.php
		 * @see http://www.php.net/manual/en/ref.pdo-oci.connection.php
		 * @see http://docs.oracle.com/cd/B10501_01/server.920/a96529/ch2.htm#100150
		 */
		public function open() {
			if ( ! $this->is_connected()) {
				try {
					$connection_string = 'oci:';
					if ( ! empty($this->data_source->hostname)) {
						$connection_string .= 'dbname=//' . $this->data_source->hostname;
						$port = $this->data_source->port; // default port is 1521
						if ( ! empty($port)) {
							$connection_string .= ':' . $port;
						}
						$connection_string .= '/' . $this->data_source->database;
					}
					else {
						$connection_string .= 'dbname='. $this->data_source->database;
					}
					if ( ! empty($this->data_source->charset)) {
						$connection_string .= ';charset=' . $this->data_source->charset;
					}
					$attributes = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
					if ($this->data_source->is_persistent()) {
						$attributes[\PDO::ATTR_PERSISTENT] = TRUE;
					}
					$this->resource = new \PDO($connection_string, $this->data_source->username, $this->data_source->password, $attributes);
				}
				catch (\PDOException $ex) {
					$this->resource = NULL;
					throw new \Leap\Core\Throwable\Database\Exception('Message: Failed to establish connection. Reason: :reason', array(':reason' => $ex->getMessage()));
				}
			}
		}

		/**
		 * This method processes an SQL statement that will return data.
		 *
		 * @access public
		 * @override
		 * @param \Leap\Core\DB\SQL\Command $sql					the SQL statement
		 * @param string $type						                the return type to be used
		 * @return \Leap\Core\DB\ResultSet                          the result set
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the query failed
		 */
		public function query(\Leap\Core\DB\SQL\Command $sql, $type = 'array') {
			return parent::query($sql, $type);
		}

		/**
		 * This method creates a data reader for query the specified SQL statement.
		 *
		 * @access public
		 * @override
		 * @param \Leap\Core\DB\SQL\Command $sql					the SQL statement
		 * @return \Leap\Core\DB\SQL\DataReader                     the SQL data reader
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the query failed
		 */
		public function reader(\Leap\Core\DB\SQL\Command $sql) {
			return parent::reader($sql);
		}

	}

}