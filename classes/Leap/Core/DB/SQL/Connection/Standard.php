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

namespace Leap\Core\DB\SQL\Connection {

	/**
	 * This class handles a standard connection.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\SQL\Connection
	 * @version 2014-01-26
	 */
	abstract class Standard extends \Leap\Core\DB\Connection\Driver {

		/**
		 * This method is for determining whether a connection is established.
		 *
		 * @access public
		 * @override
		 * @return boolean                                          whether a connection is established
		 */
		public function is_connected() {
			return is_resource($this->resource);
		}

	}

}