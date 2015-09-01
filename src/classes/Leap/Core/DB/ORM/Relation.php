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
	 * This class represents a relation in a database table.
	 *
	 * @abstract
	 * @access public
	 * @class
	 * @package Leap\Core\DB\ORM
	 * @version 2015-08-31
	 */
	abstract class Relation extends \Leap\Core\Object {

		/**
		 * This variable stores the relation's corresponding model(s).
		 *
		 * @access protected
		 * @var mixed
		 */
		protected $cache;

		/**
		 * This variable stores the relation's metadata.
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
		 * @param string $type                                      the type of relationship
		 */
		public function __construct(\Leap\Core\DB\ORM\Model $model, $type) {
			$this->model = $model;
			$this->metadata = array();
			$this->metadata['type'] = $type;
			$this->cache = NULL;
		}

		/**
		 * This destructor ensures that all references have been destroyed.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->cache);
			unset($this->metadata);
			unset($this->model);
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
				case 'result':
					if ($this->cache === NULL) {
						$this->cache = $this->load();
					}
					return $this->cache;
				break;
				default:
					if (isset($this->metadata[$key])) { return $this->metadata[$key]; }
				break;
			}
			throw new \Leap\Core\Throwable\InvalidProperty\Exception('Message: Unable to get the specified property. Reason: Property :key is either inaccessible or undefined.', array(':key' => $key));
		}

		/**
		 * This method loads the corresponding model(s).
		 *
		 * @access protected
		 * @abstract
		 * @return mixed								            the corresponding model(s)
		 */
		protected abstract function load();

		/**
		 * This method resets the relation's cache to NULL.
		 *
		 * @access public
		 */
		public function reset() {
			$this->cache = NULL;
		}

	}

}