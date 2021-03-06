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

namespace Leap\Core\DB\ORM {

	/**
	 * This class acts as an extension to the a builder class.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM
	 * @version 2015-08-31
	 */
	abstract class Builder extends \Leap\Core\Object {

		/**
		 * This variable stores an instance of the SQL builder class.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\SQL\Builder
		 */
		protected $builder;

		/**
		 * This constructor instantiates this class.
		 *
		 * @access public
		 * @param \Leap\Core\DB\SQL\Builder $builder                the SQL builder class to be extended
		 */
		public function __construct(\Leap\Core\DB\SQL\Builder $builder) {
			$this->builder = $builder;
		}

		/**
		 * This destructor ensures that all references have been destroyed.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->builder);
		}

	}

}