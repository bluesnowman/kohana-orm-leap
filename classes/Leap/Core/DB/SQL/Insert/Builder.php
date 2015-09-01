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

namespace Leap\Core\DB\SQL\Insert {

	/**
	 * This class builds an SQL insert statement.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Insert
	 * @version 2014-07-03
	 */
	abstract class Builder extends \Leap\Core\DB\SQL\Builder {

		/**
		 * This constructor instantiates this class using the specified data source.
		 *
		 * @access public
		 * @param \Leap\Core\DB\DataSource $data_source             the data source to be used
		 */
		public function __construct(\Leap\Core\DB\DataSource $data_source) {
			$data_type = '\\Leap\\Plugin\\DB\\' . $data_source->dialect . '\\Precompiler';
			$this->precompiler = new $data_type($data_source);
			$this->dialect = $data_source->dialect;
			$this->reset();
		}

		/**
		 * This method sets the associated value with the specified column.
		 *
		 * @access public
		 * @param string $column                                    the column to be set
		 * @param string $value                                     the value to be set
		 * @param integer $row                                      the index of the row
		 * @return \Leap\Core\DB\SQL\Insert\Builder                 a reference to the current instance
		 */
		public function column($column, $value, $row = 0) {
			$column = $this->precompiler->prepare_identifier($column);
			$value = $this->precompiler->prepare_value($value);
			$row = $this->precompiler->prepare_natural($row);
			$this->data['columns'][$column] = NULL;
			$this->data['rows'][$row][$column] = $value;
			return $this;
		}

		/**
		 * This method sets which table will be modified.
		 *
		 * @access public
		 * @param string $table                                     the database table to be modified
		 * @return \Leap\Core\DB\SQL\Insert\Builder                 a reference to the current instance
		 */
		public function into($table) {
			$table = $this->precompiler->prepare_identifier($table);
			$this->data['into'] = $table;
			return $this;
		}

		/**
		 * This method resets the current builder.
		 *
		 * @access public
		 * @return \Leap\Core\DB\SQL\Insert\Builder                 a reference to the current instance
		 */
		public function reset() {
			$this->data = array(
				'columns' => array(),
				'into' => NULL,
				'rows' => array(),
			);
			return $this;
		}

		/**
		 * This method sets a row of columns/values pairs.
		 *
		 * @access public
		 * @param array $values                                     the columns/values pairs to be set
		 * @param integer $row                                      the index of the row
		 * @return \Leap\Core\DB\SQL\Insert\Builder                 a reference to the current instance
		 */
		public function row(Array $values, $row = 0) {
			foreach ($values as $column => $value) {
				$this->column($column, $value, $row);
			}
			return $this;
		}

	}

}
