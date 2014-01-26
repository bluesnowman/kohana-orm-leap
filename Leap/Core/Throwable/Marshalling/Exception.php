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

namespace Leap\Core\Throwable\Marshalling {

	use Leap\Core\Throwable;

	/**
	 * This class indicates that data could not be marshalled.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\Throwable
	 * @version 2014-01-25
	 */
	class Exception extends Throwable\Runtime\Exception {}

}