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

namespace Leap\Core\DB {

	/**
	 * This class tests \Leap\Core\DB\ToolKit.
	 *
	 * @access public
	 * @class
	 * @package Leap\Core\DB
	 * @version 2015-09-01
	 *
	 * @group core
	 */
	class ToolKitTest extends \Leap\Core\UnitTest\TestCase {

		/**
		 * This method provides the test data for \Leap\Core\DB\ToolKitTest::test_regex().
		 *
		 * @access public
		 */
		public function provider_regex() {
			return array(
				array(array('spade%', NULL), '/^spade.*$/D'),
				array(array('%foot', NULL), '/^.*foot$/D'),
				array(array('spade_', NULL), '/^spade.$/D'),
				array(array('_foot', NULL), '/^.foot$/D'),
				array(array('spade_%', NULL), '/^spade.+$/D'),
				array(array('spade%_', NULL), '/^spade.*.$/D'),
				array(array('spade\%', NULL), '/^spade\\\\.*$/D'),
				array(array('spade%%', NULL), '/^spade.*$/D'),
				array(array('spade%%', '%'), '/^spade%$/D'),
				array(array('spade%%%', '%'), '/^spade%%$/D'),
				array(array('spade__', NULL), '/^spade..$/D'),
				array(array('spade_%', NULL), '/^spade.+$/D'),
				array(array('spade__%', NULL), '/^spade.+$/D'),
				array(array('spade__', '_'), '/^spade_$/D'),
				array(array('spade__', '%'), '/^spade..$/D'),
				array(array('spade%_', '%'), '/^spade_$/D'),
				array(array('spade__', '\\'), '/^spade..$/D'),
				array(array('$padefoot', NULL), '/^\$padefoot$/D'),
			);
		}

		/**
		 * This method provides the test data for \Leap\Core\DB\ToolKitTest::test_slug().
		 *
		 * @access public
		 */
		public function provider_slug() {
			return array(
				array(NULL, ''),
				array('slug', 'slug'),
				array('slug test', 'slug-test'),
				array('$slug%&#_test?', 'slug-test'),
				array('%&#_', ''),
			);
		}

		/**
		 * This method tests \Leap\Core\DB\ToolKit::regex().
		 *
		 * @access public
		 * @param mixed $test_data                          the test data
		 * @param string $expected                          the expected value
		 *
		 * @dataProvider provider_regex
		 */
		public function test_regex($test_data, $expected) {
			$this->assertSame($expected, \Leap\Core\DB\ToolKit::regex($test_data[0], $test_data[1]), 'Failed when testing regex().');
		}

		/**
		 * This method tests \Leap\Core\DB\ToolKit::slug().
		 *
		 * @access public
		 * @param mixed $test_data                          the test data
		 * @param string $expected                          the expected value
		 *
		 * @dataProvider provider_slug
		 */
		public function test_slug($test_data, $expected) {
			$this->assertSame($expected, \Leap\Core\DB\ToolKit::slug($test_data), 'Failed when testing slug().');
		}

	}

}