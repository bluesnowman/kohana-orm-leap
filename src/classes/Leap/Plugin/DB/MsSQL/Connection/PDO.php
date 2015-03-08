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

namespace Leap\Plugin\DB\MsSQL\Connection {

	/**
	 * This class handles a PDO MS SQL connection.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\MsSQL\Connection
	 * @version 2014-07-04
	 *
	 * @see http://www.php.net/manual/en/ref.pdo-dblib.php
	 */
	class PDO extends \Leap\Core\DB\SQL\Connection\PDO {

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
				return (int) $this->query(new \Leap\Core\DB\SQL\Command('SELECT SCOPE_IDENTITY() AS [id];'))->get('id', 0);
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
		 * @see http://www.php.net/manual/en/ref.pdo-dblib.php
		 */
		public function open() {
			if ( ! $this->is_connected()) {
				try {
					$config = array();
					if (extension_loaded('sqlsrv')) {
						$config['extension'] = 'sqlsrv';
						$config['host'] = 'Server';
						$config['port'] = ':';
						$config['dbname'] = 'Database';
					}
					else {
						if (extension_loaded('mssql')) {
							$config['extension'] = 'mssql';
							$config['host'] = 'host';
							$config['port'] = ',';
							$config['dbname'] = 'dbname';
							//if ( ! empty($this->data_source->charset)) {
							//	ini_set('mssql.charset', $this->data_source->charset);
							//}
						}
						else {
							$config['extension'] = 'dblib';
							$config['host'] = 'host';
							$config['port'] = ':';
							$config['dbname'] = 'dbname';
						}
					}
					$connection_string = $config['extension'] . ':' . $config['host'] . '=' . $this->data_source->host;
					$port = $this->data_source->port;
					if (!empty($port)) {
						$connection_string .= $config['port'] . $port;
					}
					$connection_string .= ';';
					$connection_string .= $config['dbname'] . '=' . $this->data_source->database;
					$attributes = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
					if ($this->data_source->is_persistent()) {
						$attributes[\PDO::ATTR_PERSISTENT] = true;
					}
					$this->resource = new \PDO($connection_string, $this->data_source->username, $this->data_source->password, $attributes);
				}
				catch (\PDOException $ex) {
					$this->resource = null;
					throw new \Leap\Core\Throwable\Database\Exception('Message: Failed to establish connection. Reason: :reason', array(':reason' => $ex->getMessage()));
				}
			}
		}

	}

}