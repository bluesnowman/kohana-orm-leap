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

namespace Leap\Core {

	/**
	 * This class is used to read a PHP config file.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core
	 * @version 2014-07-03
	 */
	class Config extends \Leap\Core\Object {

		/**
		 * This variable stores the URI associated with the file resource.
		 *
		 * @access protected
		 * @var string
		 */
		protected $uri;

		/**
		 * This constructor initializes the class with the specified resource.
		 *
		 * @access public
		 * @param string $uri                                       the URI to be processed
		 * @throws \Leap\Core\Throwable\InvalidArgument\Exception   indicates an invalid argument
		 *                                                          specified
		 * @throws \Leap\Core\Throwable\FileNotFound\Exception      indicates the file cannot be
		 *                                                          found
		 */
		public function __construct($uri) {
			if ( ! is_string($uri)) {
				throw new \Leap\Core\Throwable\InvalidArgument\Exception('Unable to handle argument. Argument must be a string.', array(':type' => gettype($uri)));
			}

			if ( ! file_exists($uri)) {
				throw new \Leap\Core\Throwable\FileNotFound\Exception('Unable to locate file. File ":uri" does not exist.', array(':uri' => $uri));
			}

			$segments = explode('.', $uri);
			$extension = end($segments);
			if (!in_array($extension,  array('inc', 'php'))) {
				throw new \Leap\Core\Throwable\InvalidArgument\Exception('Unable to process file. Expected file with either "inc" or "php" extension, but got ":extension" as an extension.', array(':extension' => $extension));
			}

			$this->uri = $uri;
		}

		/**
		 * This method returns the processed resource as a collection.
		 *
		 * @access public
		 * @return mixed                                            the resource as a collection
		 */
		public function read() {
			return include($this->uri);
		}

		/**
		 * This method return the value for the specified selector from the appropriate
		 * config file.  A selector follows dot syntax, where the first segment represents
		 * the name of the config file and the next segments represent the path to follow
		 * with the collection.
		 *
		 * @access public
		 * @static
		 * @param string $selector                                  the URI to be loaded
		 * @return \Leap\Core\Config                                an instance of this class
		 */
		public static function query($selector) {
			$index = strpos($selector, '.');
			if ($index !== FALSE) {
				$file = substr($selector, 0, $index) . '.php';
				$uri = implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'Config', $file));
				$config = new self($uri);
				$segments = explode('.', substr($selector, $index + 1));
				if (count($segments) > 0) {
					$element = $config->read();
					foreach ($segments as $segment) {
						if (is_array($element) && array_key_exists($segment, $element)) {
							$element = $element[$segment];
							continue;
						}
						return NULL;
					}
					return $element;
				}
			}
			return NULL;
		}

	}

}