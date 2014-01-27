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

namespace Leap\Core\DB\SQL {

	/**
	 * This interface provides the contract for a class representing an SQL statement.
	 *
	 * @access public
	 * @interface
	 * @package Leap\Core\DB\SQL
	 * @version 2014-01-26
	 */
	interface Statement {

		/**
		 * This method returns the SQL statement.
		 *
		 * @access public
		 * @param boolean $terminated           whether to add a semi-colon to the end
		 *                                      of the statement
		 * @return string                       the SQL statement
		 */
		public function statement($terminated = TRUE);

	}

}