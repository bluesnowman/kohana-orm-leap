<?php

/**
 * Copyright © 2011–2014 Spadefoot Team.
 * Copyright © 2012 CubedEye.
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

namespace Leap\Core\Web\HTTP\Auth\Model {

	/**
	 * This class represents a record in the "sessions" table.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\Model
	 * @version 2014-01-25
	 */
	class Session extends \Leap\Core\DB\ORM\Model {

		/**
		 * This constructor instantiates this class.
		 *
		 * @access public
		 */
		public function __construct() {
			parent::__construct();

			$this->fields = array(
				'id' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'max_length' => 24,
					'nullable' => FALSE,
				)),
				'last_active' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'max_length' => 11,
					'nullable' => FALSE,
				)),
				'contents' => new \Leap\Core\DB\ORM\Field\Text($this, array(
					'nullable' => FALSE,
				)),
			);
		}

		/**
		 * This method returns the data source name.
		 *
		 * @access public
		 * @override
		 * @static
		 * @param integer $instance                     the data source instance to be used (e.g.
		 *                                              0 = master, 1 = slave, 2 = slave, etc.)
		 * @return string                               the data source name
		 */
		public static function data_source($instance = 0) {
			return 'default';	
		}

		/**
		 * This method returns whether the primary key auto increments.
		 *
		 * @access public
		 * @override
		 * @static
		 * @return boolean                              whether the primary key auto increments
		 */
		public static function is_auto_incremented() {
			return FALSE;	
		}

		/**
		 * This method returns the primary key for the database table.
		 *
		 * @access public
		 * @override
		 * @static
		 * @return array                                the primary key
		 */
		public static function primary_key() {
			return array('id');	
		}

		/**
		 * This method returns the database table's name.
		 *
		 * @access public
		 * @override
		 * @static
		 * @return string                               the database table's name
		 */
		public static function table() {
			return 'sessions';
		}

	}

}