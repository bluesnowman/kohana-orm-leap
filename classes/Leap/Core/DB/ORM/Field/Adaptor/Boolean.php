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

namespace Leap\Core\DB\ORM\Field\Adaptor {

	/**
	 * This class represents a "boolean" adaptor for handling non-standard boolean
	 * values, such as "yes/no" decisions.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM\Field\Adaptor
	 * @version 2014-01-26
	 *
	 * @see http://www.php.net/manual/en/language.types.boolean.php
	 */
	class Boolean extends \Leap\Core\DB\ORM\Field\Adaptor {

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 * @param \Leap\Core\DB\ORM\Model $model                    a reference to the implementing model
		 * @param array $metadata                                   the adaptor's metadata
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates that an invalid field name
		 *                                                          was specified
		 */
		public function __construct(\Leap\Core\DB\ORM\Model $model, Array $metadata = array()) {
			parent::__construct($model, $metadata['field']);

			$this->metadata['values'] = (isset($metadata['values']))
				? (array) $metadata['values']
				: array('yes', 'no');
		}

		/**
		 * This method returns the value associated with the specified property.
		 *
		 * @access public
		 * @override
		 * @param string $key                                       the name of the property
		 * @return mixed                                            the value of the property
		 * @throws \Leap\Core\Throwable\InvalidProperty\Exception   indicates that the specified property is
		 *                                                          either inaccessible or undefined
		 */
		public function __get($key) {
			switch ($key) {
				case 'value':
					$value = $this->model->{$this->metadata['field']};
					if (($value !== NULL) AND ! ($value instanceof \Leap\Core\DB\SQL\Expression)) {
						$value = ($value) ? $this->metadata['values'][0] : $this->metadata['values'][1];
					}
					return $value;
				break;
				default:
					if (isset($this->metadata[$key])) { return $this->metadata[$key]; }
				break;
			}
			throw new \Leap\Core\Throwable\InvalidProperty\Exception('Message: Unable to get the specified property. Reason: Property :key is either inaccessible or undefined.', array(':key' => $key));
		}

		/**
		 * This method sets the value for the specified key.
		 *
		 * @access public
		 * @override
		 * @param string $key                                       the name of the property
		 * @param mixed $value                                      the value of the property
		 * @throws \Leap\Core\Throwable\InvalidProperty\Exception   indicates that the specified property is
		 *                                                          either inaccessible or undefined
		 */
		public function __set($key, $value) {
			switch ($key) {
				case 'value':
					if ($value !== NULL) {
						$true = $this->metadata['values'][0];
						$value = (is_string($true) AND is_string($value))
							? (strcasecmp($true, $value) == 0)
							: ($true == $value);
					}
					$this->model->{$this->metadata['field']} = $value;
				break;
				default:
					throw new \Leap\Core\Throwable\InvalidProperty\Exception('Message: Unable to set the specified property. Reason: Property :key is either inaccessible or undefined.', array(':key' => $key, ':value' => $value));
				break;
			}
		}

	}

}