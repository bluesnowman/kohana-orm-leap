<?php

namespace Leap\Core\Web\cURL {

	/**
	 * This class represents a cURL response.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\Web\cURL
	 * @version 2015-08-23
	 */
	class Response extends \Leap\Core\Object {

		/**
		 * This variable stores the header information contained in the response.
		 *
		 * @access protected
		 * @var array
		 */
		protected $header;

		/**
		 * This variable stores the body of the response.
		 *
		 * @access protected
		 * @var mixed
		 */
		protected $body;

		/**
		 * This constructor initializes the class with the specified resource.
		 *
		 * @access public
		 * @param resource $resource                                the cURL resource handler
		 * @throws \Leap\Core\Throwable\Runtime\Exception           indicates that the cURL request
		 *                                                          failed to execute
		 */
		public function __construct($resource) {
			if ( ! is_resource($resource)) {
				throw new \Leap\Core\Throwable\Runtime\Exception('Message: Failed to execute cURL request. Reason: Invalid resource.');
			}
			$body = curl_exec($resource);
			if (curl_errno($resource)) {
				$error = curl_error($resource);
				@curl_close($resource);
				throw new \Leap\Core\Throwable\Runtime\Exception('Message: Failed to execute cURL request. Reason: :error', array(':error' => $error));
			}
			$this->header = curl_getinfo($resource);
			$this->body = $body;
		}

		/**
		 * This method releases any internal references to an object.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->header);
			unset($this->body);
		}

		/**
		 * This method returns the header information contained in the response.
		 *
		 * @access public
		 * @param string $key                                       the key to be returned
		 * @return mixed                                            the header information associated
		 *                                                          with the response
		 */
		public function getHeader($key = NULL) {
			if ($key !== NULL) {
				return $this->header[$key];
			}
			return $this->header;
		}

		/**
		 * This method returns the body of the response.
		 *
		 * @access public
		 * @return mixed                                            the body of the response
		 */
		public function getBody() {
			return $this->body;
		}

	}

}