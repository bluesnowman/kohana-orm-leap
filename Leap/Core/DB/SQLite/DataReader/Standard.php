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

/**
 * This class is used to read data from an SQLite database using the standard
 * driver.
 *
 * @package Leap
 * @category SQLite
 * @version 2013-03-19
 *
 * @see http://www.php.net/manual/en/ref.sqlite.php
 *
 * @abstract
 */
abstract class Base\DB\SQLite\DataReader\Standard extends DB\SQL\DataReader\Standard {

	/**
	 * This method initializes the class.
	 *
	 * @access public
	 * @override
	 * @param DB\Connection\Driver $connection  the connection to be used
	 * @param string $sql                       the SQL statement to be queried
	 * @param integer $mode                     the execution mode to be used
	 * @throws Throwable\SQL\Exception          indicates that the query failed
	 */
	public function __construct(DB\Connection\Driver $connection, $sql, $mode = NULL) {
		$resource = $connection->get_resource();
		$command = @sqlite_query($resource, $sql);
		if ($command === FALSE) {
			throw new Throwable\SQL\Exception('Message: Failed to query SQL statement. Reason: :reason', array(':reason' => sqlite_error_string(sqlite_last_error($resource))));
		}
		$this->command = $command;
		$this->record = FALSE;
	}

	/**
	 * This method frees the command reference.
	 *
	 * @access public
	 * @override
	 */
	public function free() {
		if ($this->command !== NULL) {
			$this->command = NULL;
			$this->record = FALSE;
		}
	}

	/**
	 * This method advances the reader to the next record.
	 *
	 * @access public
	 * @override
	 * @return boolean                          whether another record was fetched
	 */
	public function read() {
		if ($this->command !== NULL) {
			$this->record = @sqlite_fetch_array($this->command, SQLITE_ASSOC);
		}
		return ($this->record !== FALSE);
	}

}
