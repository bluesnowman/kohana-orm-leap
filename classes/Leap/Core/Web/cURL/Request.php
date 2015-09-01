<?php

namespace Leap\Core\Web\cURL {

	/**
	 * This class represents a cURL request.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\Web\cURL
	 * @version 2015-08-23
	 */
	class Request extends \Leap\Core\Object {

		/**
		 * This variable stores the credentials for the authentication challenge.
		 *
		 * @access protected
		 * @var array
		 */
		protected $credentials;

		/**
		 * This variable stores the fields to be sent.
		 *
		 * @access protected
		 * @var array
		 */
		protected $fields;

		/**
		 * This variable stores the header information for the cURL request.
		 *
		 * @access protected
		 * @var array
		 */
		protected $header;

		/**
		 * This variable stores the options associated with the request.
		 *
		 * @access protected
		 * @var array
		 */
		protected $options;

		/**
		 * This variable stores the URL on which the cURL request will be made.
		 *
		 * @access protected
		 * @var array
		 */
		protected $url;

		/**
		 * This constructor initializes the class.
		 *
		 * @access public
		 * @param string $url                                       the url associated with the
		 *                                                          request
		 */
		public function __construct($url) {
			$this->credentials = array();
			$this->fields = array();
			$this->header = array();
			$this->options = array(
				CURLOPT_HEADER => FALSE,
				CURLOPT_RETURNTRANSFER => TRUE,
			);
			$this->url = array(
				CURLOPT_URL => $url,
			);
		}

		/**
		 * This method releases any internal references to an object.
		 *
		 * @access public
		 */
		public function __destruct() {
			parent::__destruct();
			unset($this->credentials);
			unset($this->fields);
			unset($this->header);
			unset($this->options);
			unset($this->url);
		}

		/**
		 * This function sets the cURL options for an authentication challenge.
		 *
		 * @access public
		 * @param string $username                                  the username to be used
		 * @param string $password                                  the password to be used
		 * @return \Leap\Core\Web\cURL\Request                      a reference to this class
		 *
		 * @see http://stackoverflow.com/questions/4753648/problems-with-username-or-pass-with-colon-when-setting-curlopt-userpwd
		 */
		public function setCredentials($username, $password) {
			if ( ! empty($username) && ! empty($password)) {
				$this->credentials[CURLOPT_HTTPAUTH] = CURLAUTH_ANY;
				$this->credentials[CURLOPT_USERPWD] = sprintf('%s:%s', $username, $password);
			}
			else {
				$this->credentials = array();
			}
			return $this;
		}

		/**
		 * This method set a field's key/value.
		 *
		 * @access public
		 * @param string $key                                       the key to be set
		 * @param mixed $value                                      the value to be set
		 * @return \Leap\Core\Web\cURL\Request                      a reference to this class
		 */
		public function setField($key, $value) {
			if ($value !== NULL) {
				$this->fields[$key] = $value;
			}
			else if (isset($this->fields[$key])) {
				unset($this->fields[$key]);
			}
			return $this;
		}

		/**
		 * This function sets a header entry.
		 *
		 * @access public
		 * @param string $key                                       the key to be set
		 * @param string $value                                     the value to be set
		 * @return \Leap\Core\Web\cURL\Request                      a reference to this class
		 */
		public function setHeader($key, $value) {
			if ($value !== NULL) {
				$this->header[$key] = $value;
			}
			else if (isset($this->header[$key])) {
				unset($this->header[$key]);
			}
			return $this;
		}

		/**
		 * This function sets an option that will be used when making the call.
		 *
		 * @access public
		 * @param string $key                                       the cURL option to be set
		 * @param string $value                                     the value associated with the option
		 * @return \Leap\Core\Web\cURL\Request                      a reference to this class
		 */
		public function setOption($key, $value) {
			if ($value !== NULL) {
				$this->options[$key] = $value;
			}
			else if (isset($this->options[$key])) {
				unset($this->options[$key]);
			}
			return $this;
		}

		/**
		 * This function sets the URL to be called.
		 *
		 * @access public
		 * @param string $url                                       the URL to be called
		 * @return \Leap\Core\Web\cURL\Request                      a reference to this class
		 */
		public function setURL($url) {
			return $this->url[CURLOPT_URL] = $url;
		}

		/**
		 * This function executes an HTTP DELETE request using cURL.
		 *
		 * @access public
		 * @param boolean $hasBody                                  whether the cURL response has a body
		 * @return \Leap\Core\Web\cURL\Response                     the response message
		 * @throws \Leap\Core\Throwable\Runtime\Exception           indicates that the request failed
		 *                                                          to execute
		 *
		 * @see http://stackoverflow.com/questions/13420952/php-curl-delete-request
		 */
		public function delete($hasBody = TRUE) {
			$resource = curl_init();

			$fields = http_build_query($this->fields);

			$action = array(
				CURLOPT_CUSTOMREQUEST => 'DELETE',
				CURLOPT_POSTFIELDS => $fields,
				CURLOPT_NOBODY => !$hasBody,
			);

			$header = array();
			foreach ($this->header as $key => $value) {
				$header[] = "{$key}: {$value}";
			}

			$options = $this->url + $this->credentials + $action + $this->options;
			if ( ! empty($header)) {
				$options[CURLOPT_HTTPHEADER] = $header;
			}

			foreach ($options as $key => $value) {
				curl_setopt($resource, $key, $value);
			}

			$response = new \Leap\Core\Web\cURL\Response($resource);

			@curl_close($resource);

			return $response;
		}

		/**
		 * This function executes an HTTP GET request using cURL.
		 *
		 * @access public
		 * @param boolean $hasBody                                  whether the cURL response has a body
		 * @return \Leap\Core\Web\cURL\Response                     the response message
		 * @throws \Leap\Core\Throwable\Runtime\Exception           indicates that the request failed
		 *                                                          to execute
		 */
		public function get($hasBody = TRUE) {
			$resource = curl_init();

			$fields = http_build_query($this->fields);

			$action = array(
				CURLOPT_NOBODY => !$hasBody,
			);

			$header = array();
			foreach ($this->header as $key => $value) {
				$header[] = "{$key}: {$value}";
			}

			$options = $this->url + $this->credentials + $action + $this->options;
			if (strlen($fields) > 0) {
				$options[CURLOPT_URL] .= ((strpos($options[CURLOPT_URL], '?') !== FALSE) ? '&' : '?') . $fields;
			}
			if ( ! empty($header)) {
				$options[CURLOPT_HTTPHEADER] = $header;
			}

			foreach ($options as $key => $value) {
				curl_setopt($resource, $key, $value);
			}

			$response = new \Leap\Core\Web\cURL\Response($resource);

			@curl_close($resource);

			return $response;
		}

		/**
		 * This function executes an HTTP HEAD request using cURL.
		 *
		 * @access public
		 * @param boolean $hasBody                                  whether the cURL response has a body
		 * @return \Leap\Core\Web\cURL\Response                     the response message
		 * @throws \Leap\Core\Throwable\Runtime\Exception           indicates that the request failed
		 *                                                          to execute
		 */
		public function head($hasBody = TRUE) {
			$resource = curl_init();

			$fields = http_build_query($this->fields);

			$action = array(
				CURLOPT_CUSTOMREQUEST => 'HEAD',
				CURLOPT_POSTFIELDS => $fields,
				CURLOPT_NOBODY => !$hasBody,
			);

			$header = array();
			foreach ($this->header as $key => $value) {
				$header[] = "{$key}: {$value}";
			}

			$options = $this->url + $this->credentials + $action + $this->options;
			if ( ! empty($header)) {
				$options[CURLOPT_HTTPHEADER] = $header;
			}

			foreach ($options as $key => $value) {
				curl_setopt($resource, $key, $value);
			}

			$response = new \Leap\Core\Web\cURL\Response($resource);

			@curl_close($resource);

			return $response;
		}

		/**
		 * This function executes an HTTP POST request using cURL.
		 *
		 * @access public
		 * @param boolean $hasBody                                  whether the cURL response has a body
		 * @return \Leap\Core\Web\cURL\Response                     the response message
		 * @throws \Leap\Core\Throwable\Runtime\Exception           indicates that the request failed
		 *                                                          to execute
		 */
		public function post($hasBody = TRUE) {
			$resource = curl_init();

			$fields = http_build_query($this->fields);

			$action = array(
				CURLOPT_POST => TRUE,
				CURLOPT_POSTFIELDS => $fields,
				CURLOPT_NOBODY => !$hasBody,
			);

			$header = array();
			foreach ($this->header as $key => $value) {
				$header[] = "{$key}: {$value}";
			}

			$options = $this->url + $this->credentials + $action + $this->options;
			if ( ! empty($header)) {
				$options[CURLOPT_HTTPHEADER] = $header;
			}

			foreach ($options as $key => $value) {
				curl_setopt($resource, $key, $value);
			}

			$response = new \Leap\Core\Web\cURL\Response($resource);

			@curl_close($resource);

			return $response;
		}

		/**
		 * This function executes an HTTP PUT request using cURL.
		 *
		 * @access public
		 * @param boolean $hasBody                                  whether the cURL response has a body
		 * @return \Leap\Core\Web\cURL\Response                     the response message
		 * @throws \Leap\Core\Throwable\Runtime\Exception           indicates that the request failed
		 *                                                          to execute
		 *
		 * @see http://www.lornajane.net/posts/2009/putting-data-fields-with-php-curl
		 */
		public function put($hasBody = TRUE) {
			$resource = curl_init();

			$fields = http_build_query($this->fields);

			$action = array(
				CURLOPT_CUSTOMREQUEST => 'PUT',
				CURLOPT_POSTFIELDS => $fields,
				CURLOPT_NOBODY => !$hasBody,
			);

			$header = array();
			$buffer = array_merge(array('Content-Length' => strlen($fields)), $this->header);
			foreach ($buffer as $key => $value) {
				$header[] = "{$key}: {$value}";
			}

			$options = $this->url + $this->credentials + $action + $this->options;
			$options[CURLOPT_HTTPHEADER] = $header;

			foreach ($options as $key => $value) {
				curl_setopt($resource, $key, $value);
			}

			$response = new \Leap\Core\Web\cURL\Response($resource);

			@curl_close($resource);

			return $response;
		}

		/**
		 * This function returns a new instance of this class.
		 *
		 * @access public
		 * @static
		 * @param string $url                                       the url associated with the
		 *                                                          request
		 * @return \Leap\Core\Web\cURL\Request                      a new instance of this class
		 */
		public static function factory($url) {
			return new static($url);
		}

	}

}
