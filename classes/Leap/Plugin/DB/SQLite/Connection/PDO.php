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

namespace Leap\Plugin\DB\SQLite\Connection {

	/**
	 * This class handles a PDO SQLite connection.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\SQLite\Connection
	 * @version 2014-07-04
	 *
	 * @see http://www.php.net/manual/en/ref.pdo-sqlite.php
	 */
	class PDO extends \Leap\Core\DB\SQL\Connection\PDO {

		/**
		 * This method opens a connection using the data source provided.
		 *
		 * @access public
		 * @override
		 * @throws \Leap\Core\Throwable\Database\Exception          indicates that there is problem with
		 *                                                          opening the connection
		 *
		 * @see http://www.php.net/manual/en/ref.pdo-sqlite.php
		 * @see http://www.sqlite.org/pragma.html#pragma_encoding
		 * @see http://stackoverflow.com/questions/263056/how-to-change-character-encoding-of-a-pdo-sqlite-connection-in-php
		 */
		public function open() {
			if ( ! $this->is_connected()) {
				try {
					$connection_string  = 'sqlite:';
					$connection_string .= $this->data_source->database;
					$attributes = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
					if ($this->data_source->is_persistent()) {
						$attributes[\PDO::ATTR_PERSISTENT] = TRUE;
					}
					$this->resource = new \PDO($connection_string, '', '', $attributes);
				}
				catch (\PDOException $ex) {
					$this->resource = NULL;
					throw new \Leap\Core\Throwable\Database\Exception('Message: Failed to establish connection. Reason: :reason', array(':reason' => $ex->getMessage()));
				}
				// "Once an encoding has been set for a database, it cannot be changed."
			}
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
				return parent::quote($string, $escape[0]);
			}

			if (is_string($escape) OR ! empty($escape)) {
				$value .= " ESCAPE '{$escape[0]}'";
			}

			return $value;
		}

	}

}