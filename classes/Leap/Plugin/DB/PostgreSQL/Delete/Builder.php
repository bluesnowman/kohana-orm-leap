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

namespace Leap\Plugin\DB\PostgreSQL\Delete {

	/**
	 * This class builds a PostgreSQL delete statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\PostgreSQL\Delete
	 * @version 2014-07-04
	 *
	 * @see http://www.postgresql.org/docs/9.0/static/sql-delete.html
	 * @see http://www.pgsql.cz/index.php/PostgreSQL_SQL_Tricks
	 * @see http://archives.postgresql.org/pgsql-hackers/2010-11/msg02023.php
	 * @see http://www.postgresql.org/docs/8.2/static/ddl-system-columns.html
	 */
	class Builder extends \Leap\Core\DB\SQL\Delete\Builder {

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
			$text = '';

			if ( ! empty($this->data['where'])) {
				$append = FALSE;
				$text .= ' WHERE ';
				foreach ($this->data['where'] as $where) {
					if ($append AND ($where[1] != \Leap\Core\DB\SQL\Builder::_CLOSING_PARENTHESIS_)) {
						$text .= " {$where[0]} ";
					}
					$text .= $where[1];
					$append = ($where[1] != \Leap\Core\DB\SQL\Builder::_OPENING_PARENTHESIS_);
				}
			}

			if ( ! empty($this->data['order_by'])) {
				$text .= ' ORDER BY ' . implode(', ', $this->data['order_by']);
			}

			if ($this->data['limit'] > 0) {
				$text .= " LIMIT {$this->data['limit']}";
			}

			if ($this->data['offset'] > 0) {
				$text .= " OFFSET {$this->data['offset']}";
			}

			if ( ! empty($text)) {
				$text = " WHERE ctid = any(array(SELECT ctid FROM {$this->data['from']}" . $text . '))';
			}

			$text = "DELETE FROM {$this->data['from']}" . $text;

			if ($terminated) {
				$text .= ';';
			}

			$command = new \Leap\Core\DB\SQL\Command($text);
			return $command;
		}

	}

}