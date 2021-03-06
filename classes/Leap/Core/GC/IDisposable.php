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

namespace Leap\Core\GC {

	/**
	 * This interface provides the contract for a class representing a disposable object.
	 *
	 * @access public
	 * @interface
	 * @package Leap\Core\GC
	 * @version 2014-05-16
	 *
	 * @see http://msdn.microsoft.com/en-us/library/system.idisposable.aspx
	 * @see http://www.codeproject.com/Articles/15360/Implementing-IDisposable-and-the-Dispose-Pattern-P
	 */
	interface IDisposable {

		/**
		 * This method assists with freeing, releasing, and resetting un-managed resources.
		 *
		 * @access public
		 * @param boolean $disposing                                whether managed resources can be disposed
		 *                                                          in addition to un-managed resources
		 *
		 * @see http://paul-m-jones.com/archives/262
		 * @see http://www.alexatnet.com/articles/optimize-php-memory-usage-eliminate-circular-references
		 */
		public function dispose($disposing = TRUE);

	}

}