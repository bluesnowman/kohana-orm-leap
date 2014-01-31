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

/**
 * This class builds an Oracle lock statement.
 *
 * @package Leap
 * @category Oracle
 * @version 2013-01-13
 *
 * @see http://docs.oracle.com/cd/B19306_01/server.102/b14200/statements_9015.htm
 * @see http://docs.oracle.com/cd/B12037_01/appdev.101/b10807/13_elems027.htm
 *
 * @abstract
 */
abstract class Base\DB\Oracle\Lock\Builder extends \Leap\Core\DB\SQL\Lock\Builder {

	/**
	 * This method acquires the required locks.
	 *
	 * @access public
	 * @override
	 * @return \Leap\Core\DB\SQL\Lock\Builder                     a reference to the current instance
	 */
	public function acquire() {
		$this->connection->begin_transaction();
		foreach ($this->data as $sql) {
			$this->connection->execute($sql);
		}
		return $this;
	}

	/**
	 * This method adds a lock definition.
	 *
	 * @access public
	 * @override
	 * @param string $table                            the table to be locked
	 * @param array $hints                             the hints to be applied
	 * @return \Leap\Core\DB\SQL\Lock\Builder                     a reference to the current instance
	 */
	public function add($table, Array $hints = NULL) {
		$table = $this->precompiler->prepare_identifier($table);
		$sql = "LOCK TABLE {$table} IN ";
		$mode = 'EXCLUSIVE';
		$wait = '';
		if ($hints !== NULL) {
			foreach ($hints as $hint) {
				if (preg_match('/^(EXCLUSIVE)|(ROW (SHARE|EXCLUSIVE))|(SHARE( (UPDATE|ROW EXCLUSIVE))?)$/i', $hint)) {
					$mode = strtoupper($hint);
				}
				else if (preg_match('/^NOWAIT$/i', $hint)) {
					$wait = ' NOWAIT';
				}
			}
		}
		$this->data[$table] = $sql . $mode . ' MODE' . $wait . ';';
		return $this;
	}

	/**
	 * This method releases all acquired locks.
	 *
	 * @access public
	 * @override
	 * @param string $method                           the method to be used to release
	 *                                                 the lock(s)
	 * @return \Leap\Core\DB\SQL\Lock\Builder                     a reference to the current instance
	 */
	public function release($method = '') {
		switch (strtoupper($method)) {
			case 'ROLLBACK':
				$this->connection->rollback();
			break;
			case 'COMMIT':
			default:
				$this->connection->commit();
			break;
		}
		return $this;
	}

}
