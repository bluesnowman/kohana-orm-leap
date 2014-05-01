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

namespace Leap\Plugin\DB\DB2\Insert {

	/**
	 * This class builds a DB2 insert statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\DB2\Insert
	 * @version 2014-04-30
	 *
	 * @see http://publib.boulder.ibm.com/infocenter/db2luw/v8/index.jsp?topic=/com.ibm.db2.udb.doc/admin/r0000970.htm
	 */
	class Builder extends \Leap\Core\DB\SQL\Insert\Builder {

		/**
		 * This method returns the SQL statement.
		 *
		 * @access public
		 * @override
		 * @param boolean $terminated                               whether to add a semi-colon to the end
		 *                                                          of the statement
		 * @return \Leap\Core\DB\SQL\Command                        the SQL statement
		 */
		public function statement($terminated = TRUE) {
			$sql = "INSERT INTO {$this->data['into']}";

			if ( ! empty($this->data['columns'])) {
				$rows = array_values($this->data['rows']);
				$rowCt = 1;
				$columns = array_keys($this->data['columns']);
				$columnCt = count($columns);
				$sql .= ' (' . implode(', ', $columns) . ') VALUES';
				for ($r = 0; $r < $rowCt; $r++) {
					if ($r > 0) {
						$sql .= ',';
					}
					$sql .= ' (';
					for ($c = 0; $c < $columnCt; $c++) {
						if ($c > 0) {
							$sql .= ', ';
						}
						$column = $columns[$c];
						$sql .= (isset($rows[$r][$column]))
							? $rows[$r][$column]
							: 'NULL';
					}
					$sql .= ')';
				}
			}

			if ($terminated) {
				$sql .= ';';
			}

			$command = new \Leap\Core\DB\SQL\Command($sql);
			return $command;
		}

	}

}