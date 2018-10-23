<?php
/**
 * Tests the user class.
 *
 * @package better-tax-handling
 */

use Niteo\WooCart\BetterTaxHandling\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {

	function setUp() {
		\WP_Mock::setUsePatchwork( true );
		\WP_Mock::setUp();
	}

	function tearDown() {
		$this->addToAssertionCount(
			\Mockery::getContainer()->mockery_getExpectationCount()
		);

		\WP_Mock::tearDown();
	}

	/**
	 * @covers \Niteo\WooCart\BetterTaxHandling\User::__construct
	 */	 
	public function testConstructor() {
		$user = new User();

		\WP_Mock::expectActionAdded( 'init', [ $user, 'init' ] );

        $user->__construct();
		\WP_Mock::assertHooksAdded();
	}

}