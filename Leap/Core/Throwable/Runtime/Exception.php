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

namespace Leap\Core\Throwable\Runtime {

	/**
	 * This class represents a Runtime Exception.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\Throwable
	 * @version 2014-01-25
	 */
	class Exception extends \Exception implements \Leap\Core\IObject {

		/**
		 * This variable stores the code associated with the exception.
		 *
		 * @access protected
		 * @var int
		 */
		protected $code;

		/**
		 * This constructor creates a new runtime exception.
		 *
		 *     throw new Throwable\Runtime\Exception('Unable to find :uri', array(':uri' => $uri));
		 *
		 * @access public
		 * @param string $message               the error message
		 * @param array $variables              translation variables
		 * @param integer $code                 the exception code
		 */
		public function __construct($message, array $variables = null, $code = 0) {
			parent::__construct(
				empty($variables) ? (string) $message : strtr( (string) $message, $variables),
				(int) $code
			);
			$this->code = (int) $code; // Known bug: http://bugs.php.net/39615
		}

		/**
		 * This method returns a copy this object.
		 *
		 * @access public
		 * @throws \Leap\Core\Throwable\UnimplementedMethod\Exception   indicates the method has not be
		 *                                                              implemented
		 */
		public function __clone() {
			throw new \Leap\Core\Throwable\UnimplementedMethod\Exception('Method ":method" has not been implemented in class ":class."', array(':class' => get_called_class(), ':method' => __FUNCTION__));
		}

		/**
		 * This method dumps information about the object.
		 *
		 * @access public
		 */
		public function __debug() {
			var_dump($this);
		}

		/**
		 * This method returns whether the specified object is equal to the called object.
		 *
		 * @access public
		 * @param \Leap\Core\IObject $object            the object to be evaluated
		 * @return boolean                              whether the specified object is equal
		 *                                              to the called object
		 */
		public function __equals($object) {
			return (($object !== NULL) && ($object instanceof \Leap\Core\Throwable\Runtime\Exception) && ($object->__hashCode() == $this->__hashCode()));
		}

		/**
		 * This method returns the name of the runtime class of this object.
		 *
		 * @access public
		 * @return string                               the name of the runtime class
		 */
		public function __getClass() {
			return get_called_class();
		}

		/**
		 * This method returns the current object's hash code.
		 *
		 * @return string                               the current object's hash code
		 */
		public function __hashCode() {
			return spl_object_hash($this);
		}

		/**
		 * This method returns the exception as a string.
		 *
		 * @access public
		 * @return string                               a string representing the exception
		 */
		public function __toString() {
			return static::text($this);
		}

		/**
		 * This method returns the exception as a string.
		 *
		 * @access public
		 * @static
		 * @param \Exception $exception                 the exception to be processed
		 * @return string                               a string representing the exception
		 */
		public static function text(\Exception $exception) {
			if ($exception !== null) {
				return sprintf('%s [ %s ]: %s ~ %s [ %d ]', get_class($exception), $exception->getCode(), strip_tags($exception->getMessage()), $exception->getFile(), $exception->getLine());
			}
			return '';
		}

	}

}
