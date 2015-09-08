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

namespace Leap\Plugin\DB\Drizzle\Insert {

	/**
	 * This class builds a Drizzle insert statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\Drizzle\Insert
	 * @version 2014-07-04
	 *
	 * @see http://dev.mysql.com/doc/refman/5.5/en/insert.html
	 */
	class Builder extends \Leap\Core\DB\SQL\Insert\Builder {

		/**
		 * This method returns the SQL command.
		 *
		 * @access public
		 * @override
		 * @param boolean $terminated                               whether to add a semi-colon to the end
		 *                                                          of the statement
		 * @return \Leap\Core\DB\SQL\Command                        the SQL command
		 */
		public function command($terminated = TRUE) {
			$text = "INSERT INTO {$this->data['into']}";

			if ( ! empty($this->data['columns'])) {
				$rows = array_values($this->data['rows']);
				$rowCt = 1;
				$columns = array_keys($this->data['columns']);
				$columnCt = count($columns);
				$text .= ' (' . implode(', ', $columns) . ') VALUES';
				for ($r = 0; $r < $rowCt; $r++) {
					if ($r > 0) {
						$text .= ',';
					}
					$text .= ' (';
					for ($c = 0; $c < $columnCt; $c++) {
						if ($c > 0) {
							$text .= ', ';
						}
						$column = $columns[$c];
						$text .= (isset($rows[$r][$column]))
							? $rows[$r][$column]
							: 'NULL';
					}
					$text .= ')';
				}
			}

			if ($terminated) {
				$text .= ';';
			}

			$command = new \Leap\Core\DB\SQL\Command($text);
			return $command;
		}

	}

}