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

namespace Leap\Core\Data\Serialization {

	use Leap\Core;
	use Leap\Core\Throwable;

	/**
	 * This class adds additional functionality to the underlying \SimpleXMLElement
	 * class.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\Data\Serialization
	 * @version 2014-01-25
	 */
	class XML extends \SimpleXMLElement implements Core\IObject {

		/**
		 * This method returns a copy this object.
		 *
		 * @access public
		 * @throws Throwable\UnimplementedMethod\Exception  indicates the method has not be
		 *                                                  implemented
		 */
		public function __clone() {
			throw new Throwable\UnimplementedMethod\Exception('Method ":method" has not been implemented in class ":class."', array(':class' => get_called_class(), ':method' => __FUNCTION__));
		}

		/**
		 * This method converts an associated array to an XML string.
		 *
		 * @access protected
		 * @static
		 * @param array $array                          the associated array to be converted
		 * @param \DOMElement $domElement               the XML DOM element
		 * @param \DOMDocument $document                the XML DOM document
		 * @return string                               a string formatted with XML
		 *
		 * @see http://darklaunch.com/2009/05/23/php-xml-encode-using-domdocument-convert-array-to-xml-json-encode
		 */
		protected static function convert_to_xml($array, $domElement = NULL, $document = NULL) {
			if ($document === NULL) {
				$document = new \DOMDocument();
				$document->formatOutput = TRUE;
				static::convert_to_xml($array, $document, $document);
				return $document->asXML();
			}
			else {
				if (is_array($array)) {
					foreach ($array as $node => $value) {
						$element = NULL;
						if (is_integer($node)) {
							$element = $domElement;
						}
						else {
							$element = $document->createElement($node);
							$domElement->appendChild($element);
						}
						static::convert_to_xml($value, $element, $document);
					}
				}
				else {
					if (is_string($array) AND preg_match('/^<!CDATA\[.*\]\]>$/', $array)) {
						$array = substr($array, 8, strlen($array) - 11);
						$element = $document->createCDATASection($array);
						$domElement->appendChild($element);
					}
					else {
						$element = $document->createTextNode($array);
						$domElement->appendChild($element);
					}
				}
			}
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
		 * This method converts an associated array to either a \SimpleXMLElement or an XML formatted
		 * string depending on the second parameter.
		 *
		 * @access public
		 * @static
		 * @param array $array                          the associated array to be converted
		 * @param boolean $as_string                    whether to return a string
		 * @return mixed                                either a \SimpleXMLElement or an XML
		 *                                              formatted string
		 */
		public static function encode(Array $array, $as_string = FALSE) {
			$contents = static::convert_to_xml($array);
			if ($as_string) {
				return $contents;
			}
			$XML = new static($contents);
			return $XML;
		}

		/**
		 * This method returns whether the specified object is equal to the called object.
		 *
		 * @access public
		 * @param Core\IObject $object                  the object to be evaluated
		 * @return boolean                              whether the specified object is equal
		 *                                              to the called object
		 */
		public function __equals($object) {
			return (($object !== NULL) && ($object instanceof Core\IObject) && ($object->__hashCode() == $this->__hashCode()));
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
		 * This method returns the hash code for the object.
		 *
		 * @access public
		 * @return string                               the hash code for the object
		 */
		public function __hashCode() {
			return spl_object_hash($this);
		}

		/**
		 * This method returns an instance of the class with the contents of the specified
		 * XML file.
		 *
		 * @access public
		 * @static
		 * @param string $uri                           the URI to the XML file
		 * @return \SimpleXMLElement                    an instance of this class
		 * @throws Throwable\InvalidArgument\Exception  indicates a data type mismatch
		 * @throws Throwable\FileNotFound\Exception     indicates that the file does not exist
		 */
		public static function load($uri) {
			if ( ! is_string($uri)) {
				throw new Throwable\InvalidArgument\Exception('Message: Wrong data type specified. Reason: Argument must be a string.', array(':type', gettype($file)));
			}

			if ( ! file_exists($uri)) {
				throw new Throwable\FileNotFound\Exception("Message: Unable to locate file. Reason: File ':file' does not exist.", array(':file', $file));
			}

			$contents = file_get_contents($uri);

			$XML = new static($contents);
			return $XML;
		}

		/**
		 * This method returns a string that represents the object.
		 *
		 * @access public
		 * @return string                               a string that represents the object
		 */
		public function __toString() {
			return (string) serialize($this);
		}

	}

}