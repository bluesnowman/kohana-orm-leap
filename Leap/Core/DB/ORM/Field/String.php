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

namespace Leap\Core\DB\ORM\Field {

	use Leap\Core\DB;
	use Leap\Core\Throwable;

	/**
	 * This class represents a "string" field in a database table.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM\Field
	 * @version 2014-01-26
	 */
	class String extends DB\ORM\Field {

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 * @param DB\ORM\Model $model                   a reference to the implementing model
		 * @param array $metadata                       the field's metadata
		 * @throws Throwable\Validation\Exception       indicates that the specified value does
		 *                                              not validate
		 */
		public function __construct(DB\ORM\Model $model, Array $metadata = array()) {
			parent::__construct($model, 'string');

			$this->metadata['max_length'] = (int) $metadata['max_length']; // the maximum length of the string

			if (isset($metadata['savable'])) {
				$this->metadata['savable'] = (bool) $metadata['savable'];
			}

			if (isset($metadata['nullable'])) {
				$this->metadata['nullable'] = (bool) $metadata['nullable'];
			}

			if (isset($metadata['filter'])) {
				$this->metadata['filter'] = (string) $metadata['filter'];
			}

			if (isset($metadata['callback'])) {
				$this->metadata['callback'] = (string) $metadata['callback'];
			}

			if (isset($metadata['enum'])) {
				$this->metadata['enum'] = (array) $metadata['enum'];
			}

			if (isset($metadata['regex'])) {
				$this->metadata['regex'] = (string) $metadata['regex'];
			}

			if (isset($metadata['control'])) {
				$this->metadata['control'] = (string) $metadata['control'];
			}

			if (isset($metadata['label'])) {
				$this->metadata['label'] = (string) $metadata['label'];
			}

			if (isset($metadata['default'])) {
				$default = $metadata['default'];
			}
			else if ( ! $this->metadata['nullable']) {
				$default = (isset($this->metadata['enum']))
					? $this->metadata['enum'][0]
					: '';
			}
			else {
				$default = (isset($this->metadata['enum']) AND ! in_array(NULL, $this->metadata['enum']))
					? $this->metadata['enum'][0]
					: NULL;
			}

			if ( ! ($default instanceof DB\SQL\Expression)) {
				if ($default !== NULL) {
					settype($default, $this->metadata['type']);
				}
				if ( ! $this->validate($default)) {
					throw new Throwable\Validation\Exception('Message: Unable to set default value for field. Reason: Value :value failed to pass validation constraints.', array(':value' => $default));
				}
			}

			$this->metadata['default'] = $default;
			$this->value = $default;
		}

		/**
		 * This method validates the specified value against any constraints.
		 *
		 * @access protected
		 * @override
		 * @param mixed $value                          the value to be validated
		 * @return boolean                              whether the specified value validates
		 */
		protected function validate($value) {
			if ($value !== NULL) {
				if (strlen($value) > $this->metadata['max_length']) {
					return FALSE;
				}
				else if (isset($this->metadata['regex']) AND ! preg_match($this->metadata['regex'], $value)) {
					return FALSE;
				}
			}
			return parent::validate($value);
		}

	}

}