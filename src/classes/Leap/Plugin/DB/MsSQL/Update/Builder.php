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

namespace Leap\Plugin\DB\MsSQL\Update {

	/**
	 * This class builds a MS SQL update statement.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\MsSQL\Update
	 * @version 2014-07-04
	 *
	 * @see http://msdn.microsoft.com/en-us/library/aa260662%28v=sql.80%29.aspx
	 */
	class Builder extends \Leap\Core\DB\SQL\Update\Builder {

		/**
		 * This method returns the SQL command.
		 *
		 * @access public
		 * @override
		 * @param boolean $terminated                               whether to add a semi-colon to the end
		 *                                                          of the statement
		 * @return \Leap\Core\DB\SQL\Command                        the SQL command
		 *
		 * @see http://stackoverflow.com/questions/655010/how-to-update-and-order-by-using-ms-sql
		 */
		public function command($terminated = TRUE) {
			$alias = ($this->data['table'] == 't0') ? 't1' : 't0';

			$text = "WITH {$alias} AS (";

			$text .= 'SELECT';

			if ($this->data['limit'] > 0) {
				$text .= " TOP {$this->data['limit']}";
			}

			$text .= " * FROM {$this->data['table']}";

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

			//if ($this->data['offset'] > 0) {
			//    $text .= " OFFSET {$this->data['offset']}";
			//}

			$text .= ") UPDATE {$alias}";

			if ( ! empty($this->data['column'])) {
				$column = $this->data['column'];

				$table = $this->data['table'];
				$table = str_replace(\Leap\Plugin\DB\MsSQL\Precompiler::_OPENING_QUOTE_CHARACTER_, '', $table);
				$table = str_replace(\Leap\Plugin\DB\MsSQL\Precompiler::_CLOSING_QUOTE_CHARACTER_, '', $table);

				$identity = \Leap\Core\DB\SQL::select('default')
					->from('INFORMATION_SCHEMA.COLUMNS')
					->column('COLUMN_NAME')
					->where('TABLE_SCHEMA', '=', 'dbo')
					->where(\Leap\Core\DB\SQL::expr('COLUMNPROPERTY(object_id(TABLE_NAME), COLUMN_NAME, \'IsIdentity\')'), '=', 1)
					->where('TABLE_NAME', '=', $table)
					->query()
					->get('COLUMN_NAME');

				if ( ! empty($identity)) {
					unset($column[\Leap\Plugin\DB\MsSQL\Precompiler::_OPENING_QUOTE_CHARACTER_ . strtolower($identity) . \Leap\Plugin\DB\MsSQL\Precompiler::_CLOSING_QUOTE_CHARACTER_]);
				}

				$text .= ' SET ' . implode(', ', array_values($column));
			}

			if ($terminated) {
				$text .= ';';
			}

			$command = new \Leap\Core\DB\SQL\Command($text);
			return $command;
		}

	}

}