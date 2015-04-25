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

namespace Leap\Plugin\DB\PostgreSQL\Select {

	/**
	 * This class builds a PostgreSQL select statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\PostgreSQL\Select
	 * @version 2015-04-25
	 *
	 * @see http://www.postgresql.org/docs/9.0/static/sql-select.html
	 */
	class Builder extends \Leap\Core\DB\SQL\Select\Builder {

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
			$text = 'SELECT ';

			if ($this->data['distinct']) {
				$text .= 'DISTINCT ';
			}

			$text .= ( ! empty($this->data['column']))
				? implode(', ', $this->data['column'])
				: $this->data['wildcard'];

			if (!empty($this->data['from'])) {
				$text .= ' FROM ' . implode(' CROSS JOIN ', $this->data['from']);
			}

			foreach ($this->data['join'] as $join) {
				$text .= " {$join[0]}";
				if ( ! empty($join[1])) {
					$text .= ' ON (' . implode(' AND ', $join[1]) . ')';
				}
				else if ( ! empty($join[2])) {
					$text .= ' USING (' . implode(', ', $join[2]) . ')';
				}
			}

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

			if ( ! empty($this->data['group_by'])) {
				$text .= ' GROUP BY ' . implode(', ', $this->data['group_by']);
			}

			if ( ! empty($this->data['having'])) {
				$append = FALSE;
				$text .= ' HAVING ';
				foreach ($this->data['having'] as $having) {
					if ($append AND ($having[1] != \Leap\Core\DB\SQL\Builder::_CLOSING_PARENTHESIS_)) {
						$text .= " {$having[0]} ";
					}
					$text .= $having[1];
					$append = ($having[1] != \Leap\Core\DB\SQL\Builder::_OPENING_PARENTHESIS_);
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

			foreach ($this->data['combine'] as $combine) {
				$text .= " {$combine}";
			}

			if ($terminated) {
				$text .= ';';
			}

			$command = new \Leap\Core\DB\SQL\Command($text);
			return $command;
		}

	}

}