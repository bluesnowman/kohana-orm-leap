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

namespace Leap\Plugin\DB\MsSQL\Delete {

	/**
	 * This class builds a MS SQL delete statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\MsSQL\Delete
	 * @version 2014-04-30
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ms189835.aspx
	 */
	class Builder extends \Leap\Core\DB\SQL\Delete\Builder {

		/**
		 * This method returns the SQL statement.
		 *
		 * @access public
		 * @override
		 * @param boolean $terminated           whether to add a semi-colon to the end
		 *                                      of the statement
		 * @return string                       the SQL statement
		 *
		 * @see http://stackoverflow.com/questions/733668/delete-the-first-record-from-a-table-in-sql-server-without-a-where-condition
		 */
		public function statement($terminated = TRUE) {
			$alias = ($this->data['from'] == 't0') ? 't1' : 't0';

			$sql = "WITH {$alias} AS (";

			$sql .= 'SELECT';

			if ($this->data['limit'] > 0) {
				$sql .= " TOP {$this->data['limit']}";
			}

			$sql .= " * FROM {$this->data['from']}";

			if ( ! empty($this->data['where'])) {
				$append = FALSE;
				$sql .= ' WHERE ';
				foreach ($this->data['where'] as $where) {
					if ($append AND ($where[1] != \Leap\Core\DB\SQL\Builder::_CLOSING_PARENTHESIS_)) {
						$sql .= " {$where[0]} ";
					}
					$sql .= $where[1];
					$append = ($where[1] != \Leap\Core\DB\SQL\Builder::_OPENING_PARENTHESIS_);
				}
			}

			if ( ! empty($this->data['order_by'])) {
				$sql .= ' ORDER BY ' . implode(', ', $this->data['order_by']);
			}

			//if ($this->data['offset'] > 0) {
			//    $sql .= " OFFSET {$this->data['offset']}";
			//}

			$sql .= ") DELETE FROM {$alias}";

			if ($terminated) {
				$sql .= ';';
			}

			$command = new \Leap\Core\DB\SQL\Command($sql);
			return $command;
		}

	}

}