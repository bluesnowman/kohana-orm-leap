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

namespace Leap\Plugin\DB\Oracle\DataReader {

	/**
	 * This class is used to read data from an Oracle database using the standard
	 * driver.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\Oracle\DataReader
	 * @version 2014-05-01
	 *
	 * @see http://php.net/manual/en/book.oci8.php
	 */
	class Standard extends \Leap\Core\DB\SQL\DataReader\Standard {

		/**
		 * This method initializes the class.
		 *
		 * @access public
		 * @override
		 * @param \Leap\Core\DB\Connection\Driver $connection       the connection to be used
		 * @param \Leap\Core\DB\SQL\Command $sql                    the SQL statement to be queried
		 * @param integer $mode                                     the execution mode to be used
		 * @throws \Leap\Core\Throwable\SQL\Exception               indicates that the query failed
		 */
		public function __construct(\Leap\Core\DB\Connection\Driver $connection, \Leap\Core\DB\SQL\Command $sql, $mode = NULL) {
			$resource = $connection->get_resource();
			$command = @oci_parse($resource, \Leap\Core\DB\SQL\Command::trim($sql->text));
			if ($command === FALSE) {
				$error = @oci_error($resource);
				$reason = (is_array($error) AND isset($error['message']))
					? $error['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to query SQL statement. Reason: :reason', array(':reason' => $reason));
			}
			if ( ! is_integer($mode)) {
				$mode = 32;
			}
			if ( ! oci_execute($command, $mode)) {
				$error = @oci_error($command);
				$reason = (is_array($error) AND isset($error['message']))
					? $error['message']
					: 'Unable to perform command.';
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to query SQL statement. Reason: :reason', array(':reason' => $reason));
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
				@oci_free_statement($this->command);
				$this->command = NULL;
				$this->record = FALSE;
			}
		}

		/**
		 * This method advances the reader to the next record.
		 *
		 * @access public
		 * @override
		 * @return boolean                                          whether another record was fetched
		 */
		public function read() {
			$this->record = @oci_fetch_assoc($this->command);
			return ($this->record !== FALSE);
		}

	}

}