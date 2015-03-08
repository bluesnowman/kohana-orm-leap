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

namespace Leap\Plugin\DB\Test {

	/**
	 * This class provides a way to access the scheme for a Test database.
	 *
	 * @access public
	 * @class
	 * @package Leap\Plugin\DB\Test
	 * @version 2014-07-04
	 */
	class Schema extends \Leap\Core\DB\Schema {

		/**
		 * This method returns an associated array of default properties for the specified
		 * SQL data type.
		 *
		 * @access public
		 * @override
		 * @param string $type                                      the SQL data type
		 * @return array                                            an associated array of default properties
		 *                                                          for the specified data type
		 *
		 * @license http://kohanaframework.org/license
		 *
		 * @see http://dev.mysql.com/doc/refman/5.0/en/data-types.html
		 */
		public function data_type($type) {
			static $types = array(
				'BLOB'                            => array('type' => 'Blob', 'max_length' => 65535),
				'BOOL'                            => array('type' => 'Boolean'),
				'BIGINT UNSIGNED'                 => array('type' => 'Integer', 'range' => array(0, '18446744073709551615')),
				'DEC UNSIGNED'                    => array('type' => 'Decimal', 'range' => array(0, NULL)),
				'DECIMAL UNSIGNED'                => array('type' => 'Decimal', 'range' => array(0, NULL)),
				'DOUBLE PRECISION UNSIGNED'       => array('type' => 'Double', 'range' => array(0, NULL)),
				'DOUBLE UNSIGNED'                 => array('type' => 'Double', 'range' => array(0, NULL)),
				'ENUM'                            => array('type' => 'String'),
				'FIXED'                           => array('type' => 'Double'),
				'FIXED UNSIGNED'                  => array('type' => 'Double', 'range' => array(0, NULL)),
				'FLOAT UNSIGNED'                  => array('type' => 'Double', 'range' => array(0, NULL)),
				'INT UNSIGNED'                    => array('type' => 'Integer', 'range' => array(0, '4294967295')),
				'INTEGER UNSIGNED'                => array('type' => 'Integer', 'range' => array(0, '4294967295')),
				'LONGBLOB'                        => array('type' => 'Blob', 'max_length' => '4294967295'),
				'LONGTEXT'                        => array('type' => 'Text', 'max_length' => '4294967295'),
				'MEDIUMBLOB'                      => array('type' => 'Blob', 'max_length' => 16777215),
				'MEDIUMINT'                       => array('type' => 'Integer', 'range' => array(-8388608, 8388607)),
				'MEDIUMINT UNSIGNED'              => array('type' => 'Integer', 'range' => array(0, 16777215)),
				'MEDIUMTEXT'                      => array('type' => 'Text', 'max_length' => 16777215),
				'NUMERIC UNSIGNED'                => array('type' => 'Decimal', 'range' => array(0, NULL)),
				'POINT'                           => array('type' => 'Binary'),
				'REAL UNSIGNED'                   => array('type' => 'Double', 'range' => array(0, NULL)),
				'SERIAL'                          => array('type' => 'Integer', 'range' => array(0, '18446744073709551615')),
				'SET'                             => array('type' => 'String'),
				'SMALLINT UNSIGNED'               => array('type' => 'Integer', 'range' => array(0, 65535)),
				'TEXT'                            => array('type' => 'Text', 'max_length' => 65535),
				'TINYBLOB'                        => array('type' => 'Blob', 'max_length' => 255),
				'TINYINT UNSIGNED'                => array('type' => 'Integer', 'range' => array(0, 255)),
				'TINYTEXT'                        => array('type' => 'String', 'max_length' => 255),
				'YEAR'                            => array('type' => 'String'),
			);

			$type = strtoupper($type);
			$type = trim(preg_replace('/ ZEROFILL/i', '', $type));

			if (isset($types[$type])) {
				return $types[$type];
			}

			return parent::data_type($type);
		}

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
		 *
		 * @see http://dev.mysql.com/doc/refman/5.5/en/show-columns.html
		 */
		public function fields($table, $like = '') {
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);

			$schema = $this->precompiler->prepare_identifier($this->data_source->database);
			$table = $this->precompiler->prepare_identifier($table);

			$text = "SHOW FULL COLUMNS FROM {$table} FROM {$schema}";

			if ( ! empty($like)) {
				$text .= ' WHERE `Field` LIKE ' . $this->precompiler->prepare_value($like);
			}

			$text .= ';';

			$reader = $connection->reader(new \Leap\Core\DB\SQL\Command($text));

			$records = array();
			$position = 0;

			while ($reader->read()) {
				$buffer = $reader->row('array');
				$type = $this->parse_type($buffer['Type']);
				$position++;
				$default = $buffer['Default'];
				if ($default == 'NULL') {
					$default = NULL;
				}
				$record = array(
					'schema' => $this->data_source->database,
					'table' => $table,
					'column' => $buffer['Field'],
					'type' => $type[0],
					'max_length' => $type[1], // max_digits, precision
					'max_decimals' => $type[2], // scale
					'attributes' => $buffer['Extra'],
					'seq_index' => $position,
					'nullable' => ($buffer['Null'] == 'YES'),
					'default' => $default,
				);
				$records[] = $record;
			}

			$reader->dispose();

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
		 * @see http://dev.mysql.com/doc/refman/5.6/en/show-index.html
		 */
		public function indexes($table, $like = '') {
			$connection = \Leap\Core\DB\Connection\Pool::instance()->get_connection($this->data_source);

			$schema = $this->precompiler->prepare_identifier($this->data_source->database);
			$table = $this->precompiler->prepare_identifier($table);

			$text = "SHOW INDEXES FROM {$table} FROM {$schema}";

			if ( ! empty($like)) {
				$text .= ' WHERE `Key_name` LIKE ' . $this->precompiler->prepare_value($like);
			}

			$text .= ';';

			$reader = $connection->reader(new \Leap\Core\DB\SQL\Command($text));

			$records = array();

			while ($reader->read()) {
				$buffer = $reader->row('array');
				$record = array(
					'schema' => $this->data_source->database,
					'table' => $buffer['Table'],
					'index' => $buffer['Key_name'],
					'column' => $buffer['Column_name'],
					'seq_index' => $buffer['Seq_in_index'],
					'ordering' => ($buffer['Collation'] == 'A') ? 'ASC' : NULL,
					'unique' => ($buffer['Non_unique'] == '0'),
					'primary' => ($buffer['Key_name'] == 'PRIMARY'),
				);
				$records[] = $record;
			}

			$reader->dispose();

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
		 * @see http://www.geeksww.com/tutorials/database_management_systems/mysql/tips_and_tricks/mysql_query_to_find_all_views_in_a_database.php
		 */
		public function tables($like = '') {
			$builder = \Leap\Core\DB\SQL::select($this->data_source)
				->column('TABLE_SCHEMA', 'schema')
				->column('TABLE_NAME', 'table')
				->column(\Leap\Core\DB\SQL::expr("'BASE'"), 'type')
				->from('INFORMATION_SCHEMA.TABLES')
				//->where('TABLE_SCHEMA', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->data_source->database)
				->where(\Leap\Core\DB\SQL::expr('UPPER(`TABLE_TYPE`)'), \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, 'BASE_TABLE')
				->order_by(\Leap\Core\DB\SQL::expr('UPPER(`TABLE_SCHEMA`)'))
				->order_by(\Leap\Core\DB\SQL::expr('UPPER(`TABLE_NAME`)'));

			if ( ! empty($like)) {
				$builder->where('TABLE_NAME', \Leap\Core\DB\SQL\Operator::_LIKE_, $like);
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
		 * @see http://dev.mysql.com/doc/refman/5.6/en/triggers-table.html
		 * @see http://dev.mysql.com/doc/refman/5.6/en/show-triggers.html
		 */
		public function triggers($table, $like = '') {
			$builder = \Leap\Core\DB\SQL::select($this->data_source)
				->column('EVENT_OBJECT_SCHEMA', 'schema')
				->column('EVENT_OBJECT_TABLE', 'table')
				->column('TRIGGER_NAME', 'trigger')
				->column('EVENT_MANIPULATION', 'event')
				->column('ACTION_TIMING', 'timing')
				->column('ACTION_ORIENTATION', 'per')
				->column('ACTION_STATEMENT', 'action')
				->column('ACTION_ORDER', 'seq_index')
				->column('CREATED', 'created')
				->from('INFORMATION_SCHEMA.TRIGGERS')
				//->where('EVENT_OBJECT_SCHEMA', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->data_source->database)
				->where(\Leap\Core\DB\SQL::expr('UPPER(`EVENT_OBJECT_TABLE`)'), \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $table)
				->order_by(\Leap\Core\DB\SQL::expr('UPPER(`EVENT_OBJECT_SCHEMA`)'))
				->order_by(\Leap\Core\DB\SQL::expr('UPPER(`EVENT_OBJECT_TABLE`)'))
				->order_by(\Leap\Core\DB\SQL::expr('UPPER(`TRIGGER_NAME`)'))
				->order_by('ACTION_ORDER');

			if ( ! empty($like)) {
				$builder->where('TRIGGER_NAME', \Leap\Core\DB\SQL\Operator::_LIKE_, $like);
			}

			return $builder->query();
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
		 * @see http://www.geeksww.com/tutorials/database_management_systems/mysql/tips_and_tricks/mysql_query_to_find_all_views_in_a_database.php
		 */
		public function views($like = '') {
			$builder = \Leap\Core\DB\SQL::select($this->data_source)
				->column('TABLE_SCHEMA', 'schema')
				->column('TABLE_NAME', 'table')
				->column(\Leap\Core\DB\SQL::expr("'VIEW'"), 'type')
				->from('INFORMATION_SCHEMA.TABLES')
				//->where('TABLE_SCHEMA', \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, $this->data_source->database)
				->where(\Leap\Core\DB\SQL::expr('UPPER(`TABLE_TYPE`)'), \Leap\Core\DB\SQL\Operator::_EQUAL_TO_, 'VIEW')
				->order_by(\Leap\Core\DB\SQL::expr('UPPER(`TABLE_SCHEMA`)'))
				->order_by(\Leap\Core\DB\SQL::expr('UPPER(`TABLE_NAME`)'));

			if ( ! empty($like)) {
				$builder->where('TABLE_NAME', \Leap\Core\DB\SQL\Operator::_LIKE_, $like);
			}

			return $builder->query();
		}

	}

}