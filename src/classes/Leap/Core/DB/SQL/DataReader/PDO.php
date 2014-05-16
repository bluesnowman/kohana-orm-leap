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

namespace Leap\Core\DB\SQL\DataReader {

	/**
	 * This class is used to read data from an SQL database using the PDO
	 * driver.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\DataReader
	 * @version 2014-05-16
	 */
	abstract class PDO extends \Leap\Core\DB\SQL\DataReader {

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
			$command = @$resource->query($sql->text);
			if ($command === FALSE) {
				throw new \Leap\Core\Throwable\SQL\Exception('Message: Failed to query SQL statement. Reason: :reason', array(':reason' => $resource->errorInfo()));
			}
			$this->command = $command;
			$this->record = FALSE;
		}

		/**
		 * This method assists with freeing, releasing, and resetting un-managed resources.
		 *
		 * @access public
		 * @param boolean $disposing                                whether managed resources can be disposed
		 *                                                          in addition to un-managed resources
		 */
		public function dispose($disposing = TRUE) {
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
		 * @return boolean                                          whether another record was fetched
		 */
		public function read() {
			if ($this->command !== NULL) {
				$this->record = @$this->command->fetch(\PDO::FETCH_ASSOC);
			}
			return ($this->record !== FALSE);
		}

	}

}