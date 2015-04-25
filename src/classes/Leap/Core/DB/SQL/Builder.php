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

namespace Leap\Core\DB\SQL {

	/**
	 * This class provides the base functionality for an SQL command.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL
	 * @version 2014-07-04
	 */
	abstract class Builder extends \Leap\Core\Object implements \Leap\Core\DB\SQL\Statement {

		/**
		 * This constant represents a closing parenthesis.
		 *
		 * @access public
		 * @const string
		 */
		const _CLOSING_PARENTHESIS_ = ')';

		/**
		 * This constant represents an opening parenthesis.
		 *
		 * @access public
		 * @const string
		 */
		const _OPENING_PARENTHESIS_ = '(';

		/**
		 * This variable stores the build data for the SQL command.
		 *
		 * @access protected
		 * @var array
		 */
		protected $data;

		/**
		 * This variable stores the name of the SQL dialect being used.
		 *
		 * @access protected
		 * @var string
		 */
		protected $dialect;

		/**
		 * This variable stores a reference to the pre-compiler.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\SQL\Precompiler
		 */
		protected $precompiler;

		/**
		 * This method returns the raw SQL command.
		 *
		 * @access public
		 * @override
		 * @return string                                           the raw SQL command
		 */
		public function __toString() {
			return $this->command()->__toString();
		}

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * This method returns a new instance of the calling class.
		 *
		 * @access public
		 * @static
		 * @param \Leap\Core\DB\DataSource $data_source             the data source to be used
		 * @return \Leap\Core\DB\SQL\Builder                        a new instance of the calling class
		 */
		public static function factory(\Leap\Core\DB\DataSource $data_source) {
			$data_type = get_called_class();
			$object = new $data_type($data_source);
			return $object;
		}

	}

}