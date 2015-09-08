<?php

/**
 * Copyright © 2011–2015 Spadefoot Team.
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
	 * This class represents a record in the "users" table.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\Model
	 * @version 2014-01-25
	 */
	class User extends \Leap\Core\DB\ORM\Model {

		/**
		 * This constructor instantiates this class.
		 *
		 * @access public
		 */
		public function __construct() {
			parent::__construct();

			$this->fields = array(
				'id' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'max_length' => 11,
					'nullable' => FALSE,
					'unsigned' => TRUE,
				)),
				'email' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'max_length' => 254,
					'nullable' => FALSE,
				)),
				'username' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'default' => '',
					'max_length' => 32,
					'nullable' => FALSE,
				)),
				'password' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'max_length' => 64,
					'nullable' => FALSE,
				)),
				// Personal Details
				'firstname' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'default' => NULL,
					'max_length' => 35,
					'nullable' => TRUE,
				)),
				'lastname' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'default' => NULL,
					'max_length' => 50,
					'nullable' => TRUE,
				)),
				// Account Status Details
				'activated' => new \Leap\Core\DB\ORM\Field\Boolean($this, array(
					'default' => TRUE,
					'nullable' => FALSE,
				)),
				'banned' => new \Leap\Core\DB\ORM\Field\Boolean($this, array(
					'default' => FALSE,
					'nullable' => FALSE,
				)),
				'ban_reason' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'default' => NULL,
					'max_length' => 255,
					'nullable' => TRUE,
				)),
				// Account Utility Details
				'new_password_key' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'default' => NULL,
					'max_length' => 64,
					'nullable' => TRUE,
				)),
				'new_password_requested' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'default' => NULL,
					'max_length' => 11,
					'nullable' => TRUE,
				)),
				'new_email' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'default' => NULL,
					'max_length' => 254,
					'nullable' => TRUE,
				)),
				'new_email_key' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'default' => NULL,
					'max_length' => 64,
					'nullable' => TRUE,
				)),
				// Account Metrics Details
				'logins' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'default' => 0,
					'max_length' => 10,
					'nullable' => FALSE,
					'unsigned' => TRUE,
				)),
				'last_login' => new \Leap\Core\DB\ORM\Field\Integer($this, array(
					'default' => NULL,
					'max_length' => 10,
					'nullable' => TRUE,
					'unsigned' => TRUE,
				)),
				'last_ip' => new \Leap\Core\DB\ORM\Field\String($this, array(
					'default' => NULL,
					'max_length' => 39,
					'nullable' => TRUE,
				)),
			);

			$this->adaptors = array(
				'last_login_formatted' => new \Leap\Core\DB\ORM\Field\Adaptor\DateTime($this, array(
					'field' => 'last_login',
				)),
				'new_password_requested_formatted' => new \Leap\Core\DB\ORM\Field\Adaptor\DateTime($this, array(
					'field' => 'new_password_requested',
				)),
			);

			$this->relations = array(
				'user_roles' => new \Leap\Core\DB\ORM\Relation\HasMany($this, array(
					'child_key' => array('user_id'),
					'child_model' => '\\Leap\\Core\\Web\\HTTP\\Auth\\Model\\User\\Role',
					'parent_key' => array('id'),
				)),
				'user_token' => new \Leap\Core\DB\ORM\Relation\HasMany($this, array(
					'child_key' => array('user_id'),
					'child_model' => '\\Leap\\Core\\Web\\HTTP\\Auth\\Model\\User\\Token',
					'parent_key' => array('id'),
				)),
			);
		}

		/**
		 * This method completes the user's login.
		 *
		 * @access public
		 */
		public function complete_login() {
			$this->logins++;
			$this->last_login = time();
			$this->last_ip = \Request::$client_ip;
			$this->save();
		}

		/**
		 * This method returns the data source name.
		 *
		 * @access public
		 * @override
		 * @static
		 * @param integer $instance                                 the data source instance to be used (e.g.
		 *                                                          0 = master, 1 = slave, 2 = slave, etc.)
		 * @return string                                           the data source name
		 */
		public static function data_source($instance = 0) {
			return 'default';	
		}

		/**
		 * This method returns the primary key for the database table.
		 *
		 * @access public
		 * @override
		 * @static
		 * @return array                                            the primary key
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
		 * @return string                                           the database table's name
		 */
		public static function table() {
			return 'users';	
		}

	}

}