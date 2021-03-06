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
	 * This class represents a "number" adaptor for a handling UOM conversions.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM\Field\Adaptor
	 * @version 2014-06-04
	 */
	class UOM extends \Leap\Core\DB\ORM\Field\Adaptor {

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 * @param \Leap\Core\DB\ORM\Model $model                    a reference to the implementing model
		 * @param array $metadata                                   the adaptor's metadata
		 * @throws \Leap\Core\Throwable\Runtime\Exception           indicates that error occurred when loading
		 *                                                          a configuration
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates that an invalid field name
		 *                                                          was specified
		 */
		public function __construct(\Leap\Core\DB\ORM\Model $model, Array $metadata = array()) {
			parent::__construct($model, $metadata['field']);

			$this->metadata['units'] = array();

			$selector = 'UOM.' . strtolower($metadata['measurement'] . '.' . $metadata['units'][0]);

			if (($unit = \Leap\Core\Config::query($selector)) === NULL) {
				throw new \Leap\Core\Throwable\Runtime\Exception('Message: Unable to load configuration. Reason: Configuration group :group is undefined.', array(':group' => $selector));
			}

			$this->metadata['units'][0] = $unit; // field's unit

			$selector = 'UOM.' . strtolower($metadata['measurement'] . '.' . $metadata['units'][1]);

			if (($unit = \Leap\Core\Config::query($selector)) === NULL) {
				throw new \Leap\Core\Throwable\Runtime\Exception('Message: Unable to load configuration. Reason: Configuration group :group is undefined.', array(':group' => $selector));
			}

			$this->metadata['units'][1] = $unit; // adaptor's unit
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
						$value = static::convert($value, $this->metadata['units'][0], $this->metadata['units'][1]);
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
						$value = static::convert($value, $this->metadata['units'][1], $this->metadata['units'][0]);
					}
					$this->model->{$this->metadata['field']} = $value;
				break;
				default:
					throw new \Leap\Core\Throwable\InvalidProperty\Exception('Message: Unable to set the specified property. Reason: Property :key is either inaccessible or undefined.', array(':key' => $key, ':value' => $value));
				break;
			}
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * This method converts a value's units.
		 *
		 * @access protected
		 * @static
		 * @param double $value                                     the value to converted
		 * @param string $units0                                    the value's starting units
		 * @param string $units1                                    the value's ending units
		 * @return double                                           the new value
		 */
		protected static function convert($value, $units0, $units1) {
			return ($value * static::parse($units0)) / static::parse($units1);
		}

		/**
		 * This method parses a mathematical expression to evaluate it.
		 *
		 * @access protected
		 * @static
		 * @param string $expr                                      the expression to be evaluated
		 * @return double                                           the value
		 *
		 * @see http://de.php.net/eval
		 */
		protected static function parse($expr) {
			$expr = preg_replace("/[^0-9+\-.*\/()%]/", "", $expr);
			$expr = preg_replace("/([+-])([0-9]{1})(%)/", "*(1\$1.0\$2)", $expr);
			$expr = preg_replace("/([+-])([0-9]+)(%)/", "*(1\$1.\$2)", $expr);
			$expr = preg_replace("/([0-9]+)(%)/", ".\$1", $expr);
			if ($expr == "") {
				$value = 0;
			}
			else {
				eval("\$value=" . $expr . ";");
			}
			return $value;
		}

	}

}