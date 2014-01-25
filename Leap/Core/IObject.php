<?php

namespace Leap\Core {

	/**
	 * This interface defines the contract for an object.
	 *
	 * @access public
	 * @interface
	 * @package Leap\Core
	 * @version 2014-01-25
	 */
	interface IObject {

		/**
		 * This method returns a copy this object.
		 *
		 * @access public
		 */
		public function __clone();

		/**
		 * This method dumps information about the object.
		 *
		 * @access public
		 */
		public function __debug();

		/**
		 * This method returns whether the specified object is equal to the called object.
		 *
		 * @access public
		 * @param IObject $object                       the object to be evaluated
		 * @return boolean                              whether the specified object is equal
		 *                                              to the called object
		 */
		public function __equals($object);

		/**
		 * This method returns the name of the runtime class of this object.
		 *
		 * @access public
		 * @return string                               the name of the runtime class
		 */
		public function __getClass();

		/**
		 * This method returns the hash code for the object.
		 *
		 * @access public
		 * @return string                               the hash code for the object
		 */
		public function __hashCode();

		/**
		 * This method returns a string that represents the object.
		 *
		 * @access public
		 * @return string                               a string that represents the object
		 */
		public function __toString();

	}

}