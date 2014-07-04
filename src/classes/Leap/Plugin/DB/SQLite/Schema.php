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

namespace Leap\Plugin\DB\SQLite {

	/**
	 * This class provides a way to access the scheme for an SQLite database.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\SQLite
	 * @version 2014-07-04
	 */
	class Schema extends \Leap\Core\DB\Schema {

		/**
		 * This method returns a result set of fields for the specified table.
		 *
		 * +---------------+---------------+------------------------------------------------------------+
		 * | field         | data type     | description                                                |
		 * +---------------+---------------+------------------------------------------------------------+
		 * | schema        | string        | The name of the schema that contains the table.            |
		 * | table         | string        | The name of the table.                                     |
		 * | column        | string        | The name of the column.                                    |
		 * | type          | string        | The data type of the column.                               |
		 * | max_length    | integer       | The max length, max digits, or precision of the column.    |
		 * | max_decimals  | integer       | The max decimals or scale of the column.                   |
		 * | attributes    | string        | Any additional attributes associated with the column.      |
		 * | seq_index     | integer       | The sequence index of the column.                          |
		 * | nullable      | boolean       | Indicates whether the column can contain a NULL value.     |
		 * | default       | mixed         | The default value of the column.                           |
		 * +---------------+---------------+------------------------------------------------------------+
		 *
		 * @access public
		 * @override
		 * @param string $table                                     the table to evaluated
		 * @param string $like                                      a like constraint on the query
		 * @return \Leap\Core\DB\ResultSet                          an array of fields within the specified
		 *                                                          table
		 */
		public function fields($table, $like = '') {
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);

			$path_info = pathinfo($this->data_source->database);
			$schema = $path_info['filename'];

			$table = trim(preg_replace('/[^a-z0-9$_ ]/i', '', $table));

			$text = "PRAGMA TABLE_INFO('{$table}');";

			$fields = $connection->query(new \Leap\Core\DB\SQL\Command($text)); // cid, name, type, notnull, dflt_value, pk

			$records = array();

			foreach ($fields as $field) {
				if (empty($like) OR preg_match(\Leap\Core\DB\ToolKit::regex($like), $field['name'])) {
					$type = $this->parse_type($field['type']);

					$record = array(
						'schema' => $schema,
						'table' => $table,
						'column' => $field['name'],
						'type' => $type[0],
						'max_length' => $type[1], // max_digits, precision
						'max_decimals' => $type[2], // scale
						'attributes' => '',
						'seq_index' => $field['cid'],
						'nullable' => !$field['notnull'],
						'default' => $field['dflt_value'],
					);

					$records[] = $record;
				}
			}

			$results = new \Leap\Core\DB\ResultSet($records);

			return $results;
		}

		/**
		 * This method returns a result set of indexes for the specified table.
		 *
		 * +---------------+---------------+------------------------------------------------------------+
		 * | field         | data type     | description                                                |
		 * +---------------+---------------+------------------------------------------------------------+
		 * | schema        | string        | The name of the schema that contains the table.            |
		 * | table         | string        | The name of the table.                                     |
		 * | index         | string        | The name of the index.                                     |
		 * | column        | string        | The name of the column.                                    |
		 * | seq_index     | integer       | The sequence index of the index.                           |
		 * | ordering      | string        | The ordering of the index.                                 |
		 * | unique        | boolean       | Indicates whether index on column is unique.               |
		 * | primary       | boolean       | Indicates whether index on column is a primary key.        |
		 * +---------------+---------------+------------------------------------------------------------+
		 *
		 * @access public
		 * @override
		 * @param string $table                                     the table to evaluated
		 * @param string $like                                      a like constraint on the query
		 * @return \Leap\Core\DB\ResultSet                          a result set of indexes for the specified
		 *                                                          table
		 *
		 * @see http://stackoverflow.com/questions/157392/how-do-i-find-out-if-a-sqlite-index-is-unique-with-sql
		 * @see http://marc.info/?l=sqlite-users&m=107868394932015
		 * @see http://my.safaribooksonline.com/book/databases/sql/9781449394592/sqlite-pragmas/id3054722
		 * @see http://my.safaribooksonline.com/book/databases/sql/9781449394592/sqlite-pragmas/id3054537
		 */
		public function indexes($table, $like = '') {
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);

			$path_info = pathinfo($this->data_source->database);
			$schema = $path_info['filename'];

			$table = trim(preg_replace('/[^a-z0-9$_ ]/i', '', $table));

			$text = "PRAGMA INDEX_LIST('{$table}');";

			$indexes = $connection->query(new \Leap\Core\DB\SQL\Command($text));

			$records = array();

			foreach ($indexes as $index) {
				if (empty($like) OR preg_match(\Leap\Core\DB\ToolKit::regex($like), $index['name'])) {
					$reader = $connection->reader("PRAGMA INDEX_INFO('{$index['name']}');");
					while ($reader->read()) {
						$column = $reader->row('array');
						$record = array(
							'schema' => $schema,
							'table' => $table,
							'index' => $index['name'],
							'column' => $column['name'],
							'seq_index' => $column['cid'],
							'ordering' => NULL,
							'unique' => $index['unique'],
							'primary' => 0,
						);
						$records[] = $record;
					}
					$reader->dispose();
				}
			}

			$results = new \Leap\Core\DB\ResultSet($records);

			return $results;
		}

		/**
		 * This method returns a result set of database tables.
		 *
		 * +---------------+---------------+------------------------------------------------------------+
		 * | field         | data type     | description                                                |
		 * +---------------+---------------+------------------------------------------------------------+
		 * | schema        | string        | The name of the schema that contains the table.            |
		 * | table         | string        | The name of the table.                                     |
		 * | type          | string        | The type of table.                                         |
		 * +---------------+---------------+------------------------------------------------------------+
		 *
		 * @access public
		 * @override
		 * @param string $like                                      a like constraint on the query
		 * @return \Leap\Core\DB\ResultSet                          a result set of database tables
		 *
		 * @see http://www.sqlite.org/faq.html#q7
		 */
		public function tables($like = '') {
			$path_info = pathinfo($this->data_source->database);
			$schema = $path_info['filename'];

			$builder = \Leap\Core\DB\SQL::select($this->data_source)
				->column(\Leap\Core\DB\SQL::expr("'{$schema}'"), 'schema')
				->column('name', 'table')
				->column(\Leap\Core\DB\SQL::expr("'BASE'"), 'type')
				->from(\Leap\Core\DB\SQL::expr('(SELECT * FROM [sqlite_master] UNION ALL SELECT * FROM [sqlite_temp_master])'))
				->where('type', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, 'table')
				->where('name', \Leap\Core\DB\SQL\Operator::_NOT_LIKE_, 'sqlite_%')
				->order_by(\Leap\Core\DB\SQL::expr('UPPER([name])'));

			if ( ! empty($like)) {
				$builder->where('name', \Leap\Core\DB\SQL\Operator::_LIKE_, $like);
			}

			return $builder->query();
		}

		/**
		 * This method returns a result set of triggers for the specified table.
		 *
		 * +---------------+---------------+------------------------------------------------------------+
		 * | field         | data type     | description                                                |
		 * +---------------+---------------+------------------------------------------------------------+
		 * | schema        | string        | The name of the schema that contains the table.            |
		 * | table         | string        | The name of the table to which the trigger is defined on.  |
		 * | trigger       | string        | The name of the trigger.                                   |
		 * | event         | string        | 'INSERT', 'DELETE', or 'UPDATE'                            |
		 * | timing        | string        | 'BEFORE', 'AFTER', or 'INSTEAD OF'                         |
		 * | per           | string        | 'ROW', 'STATEMENT', or 'EVENT'                             |
		 * | action        | string        | The action that will be triggered.                         |
		 * | seq_index     | integer       | The sequence index of the trigger.                         |
		 * | created       | date/time     | The date/time of when the trigger was created.             |
		 * +---------------+---------------+------------------------------------------------------------+
		 *
		 * @access public
		 * @override
		 * @param string $table                                     the table to evaluated
		 * @param string $like                                      a like constraint on the query
		 * @return \Leap\Core\DB\ResultSet                          a result set of triggers for the specified
		 *                                                          table
		 *
		 * @see http://www.sqlite.org/lang_createtrigger.html
		 * @see http://linuxgazette.net/109/chirico1.html
		 */
		public function triggers($table, $like = '') {
			$path_info = pathinfo($this->data_source->database);
			$schema = $path_info['filename'];

			$builder = \Leap\Core\DB\SQL::select($this->data_source)
				->column(\Leap\Core\DB\SQL::expr("'{$schema}'"), 'schema')
				->column('tbl_name', 'table')
				->column('name', 'trigger')
				->column(\Leap\Core\DB\SQL::expr('NULL'), 'event')
				->column(\Leap\Core\DB\SQL::expr('NULL'), 'timing')
				->column(\Leap\Core\DB\SQL::expr("'ROW'"), 'per')
				->column('sql', 'action')
				->column(\Leap\Core\DB\SQL::expr('0'), 'seq_index')
				->column(\Leap\Core\DB\SQL::expr('NULL'), 'created')
				->from(\Leap\Core\DB\SQL::expr('(SELECT * FROM [sqlite_master] UNION ALL SELECT * FROM [sqlite_temp_master])'))
				->where('type', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, 'trigger')
				->where('tbl_name', \Leap\Core\DB\SQL\Operator::_NOT_LIKE_, 'sqlite_%')
				->order_by(\Leap\Core\DB\SQL::expr('UPPER([tbl_name])'))
				->order_by(\Leap\Core\DB\SQL::expr('UPPER([name])'));

			if ( ! empty($like)) {
				$builder->where('[name]', \Leap\Core\DB\SQL\Operator::_LIKE_, $like);
			}

			$reader = $builder->reader();

			$records = array();

			while ($reader->read()) {
				$record = $reader->row('array');
				if (isset($record['action'])) {
					$text = \Leap\Core\DB\SQL\Command::trim($record['action']);

					if (preg_match('/\s+INSERT\s+/i', $text)) {
						$record['event'] = 'INSERT';
					}
					else if (preg_match('/\s+UPDATE\s+/i', $text)) {
						$record['event'] = 'UPDATE';
					}
					else if (preg_match('/\s+DELETE\s+OF\s+/i', $text)) {
						$record['event'] = 'DELETE';
					}

					if (preg_match('/\s+BEFORE\s+/i', $text)) {
						$record['timing'] = 'BEFORE';
					}
					else if (preg_match('/\s+AFTER\s+/i', $text)) {
						$record['timing'] = 'AFTER';
					}
					else if (preg_match('/\s+INSTEAD\s+OF\s+/i', $text)) {
						$record['timing'] = 'INSTEAD OF';
					}

					$offset = stripos($text, 'BEGIN') + 5;
					$length = (strlen($text) - $offset) - 3;
					$record['action'] = \Leap\Core\DB\SQL\Command::trim(substr($text, $offset, $length));
				}
				$records[] = $record;
			}

			$reader->dispose();

			$results = new \Leap\Core\DB\ResultSet($records);

			return $results;
		}

		/**
		 * This method returns a result set of database views.
		 *
		 * +---------------+---------------+------------------------------------------------------------+
		 * | field         | data type     | description                                                |
		 * +---------------+---------------+------------------------------------------------------------+
		 * | schema        | string        | The name of the schema that contains the table.            |
		 * | table         | string        | The name of the table.                                     |
		 * | type          | string        | The type of table.                                         |
		 * +---------------+---------------+------------------------------------------------------------+
		 *
		 * @access public
		 * @override
		 * @param string $like                                      a like constraint on the query
		 * @return \Leap\Core\DB\ResultSet                          a result set of database views
		 *
		 * @see http://www.sqlite.org/faq.html#q7
		 */
		public function views($like = '') {
			$path_info = pathinfo($this->data_source->database);
			$schema = $path_info['filename'];

			$builder = \Leap\Core\DB\SQL::select($this->data_source)
				->column(\Leap\Core\DB\SQL::expr("'{$schema}'"), 'schema')
				->column('name', 'table')
				->column(\Leap\Core\DB\SQL::expr("'VIEW'"), 'type')
				->from(\Leap\Core\DB\SQL::expr('(SELECT * FROM [sqlite_master] UNION ALL SELECT * FROM [sqlite_temp_master])'))
				->where('type', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, 'view')
				->where('name', \Leap\Core\DB\SQL\Operator::_NOT_LIKE_, 'sqlite_%')
				->order_by(\Leap\Core\DB\SQL::expr('UPPER([name])'));

			if ( ! empty($like)) {
				$builder->where('name', \Leap\Core\DB\SQL\Operator::_LIKE_, $like);
			}

			return $builder->query();
		}

	}

}