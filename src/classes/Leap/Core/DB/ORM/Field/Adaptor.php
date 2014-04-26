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

	/**
	 * This class represents an adaptor for a field in a database table.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM\Field
	 * @version 2014-01-26
	 */
	abstract class Adaptor extends \Leap\Core\Object {

		/**
		 * This variable stores the adaptor's metadata.
		 *
		 * @access protected
		 * @var array
		 */
		protected $metadata;

		/**
		 * This variable stores a reference to the implementing model.
		 *
		 * @access protected
		 * @var \Leap\Core\DB\ORM\Model
		 */
		protected $model;

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 * @param \Leap\Core\DB\ORM\Model $model                    a reference to the implementing model
		 * @param string $field                                     the name of field in the database table
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates that an invalid field name
		 *                                                          was specified
		 */
		public function __construct(\Leap\Core\DB\ORM\Model $model, $field) {
			if ( ! is_string($field) OR $model->is_adaptor($field) OR $model->is_alias($field) OR ! $model->is_field($field) OR $model->is_relation($field)) {
				throw new \Leap\Core\Throwable\InvalidArgument\Exception('Message: Invalid field name defined. Reason: Field name either is not a field or is already defined.', array(':field' => $field));
			}
			$this->model = $model;
			$this->metadata['field'] = $field;
		}

		/**
		 * This destructor ensures that all references have been destroyed.
		 *
		 * @access public
		 */
		public function __destruct() {
			unset($this->metadata);
			unset($this->model);
		}

		/**
		 * This method returns the value associated with the specified property.
		 *
		 * @access public
		 * @abstract
		 * @param string $key                                       the name of the property
		 * @return mixed                                            the value of the property
		 * @throws \Leap\Core\Throwable\InvalidProperty\Exception   indicates that the specified property is
		 *                                                          either inaccessible or undefined
		 */
		public abstract function __get($key);

		/**
		 * This method sets the value for the specified key.
		 *
		 * @access public
		 * @abstract
		 * @param string $key                                       the name of the property
		 * @param mixed $value                                      the value of the property
		 * @throws \Leap\Core\Throwable\InvalidProperty\Exception   indicates that the specified property is
		 *                                                          either inaccessible or undefined
		 */
		public abstract function __set($key, $value);

	}

}