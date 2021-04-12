<?php
/**
 * Tests the user class.
 *
 * @package eu-vat-b2b-taxes
 */

namespace Niteo\WooCart\EUVatTaxes;

use Niteo\WooCart\EUVatTaxes\UserView;
use PHPUnit\Framework\TestCase;

/**
 * Tests UserView class functions in isolation.
 *
 * @package Niteo\WooCart\EUVatTaxes
 * @coversDefaultClass \Niteo\WooCart\EUVatTaxes\UserView
 */
class UserViewTest extends TestCase {

	function setUp() : void {
		\WP_Mock::setUsePatchwork( true );
		\WP_Mock::setUp();
	}

	function tearDown() : void {
		$this->addToAssertionCount(
			\Mockery::getContainer()->mockery_getExpectationCount()
		);

		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstructor() {
		$user = new UserView();

		\WP_Mock::expectActionAdded( 'init', array( $user, 'init' ) );

		$user->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers ::__construct
	 * @covers ::init
	 */
	public function testInit() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'times'  => 7,
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'times'  => 7,
				'return' => true,
			)
		);

		\WP_Mock::expectFilterAdded( 'woocommerce_billing_fields', array( $user, 'checkout_fields' ) );
		\WP_Mock::expectFilterAdded( 'woocommerce_product_get_tax_class', array( $user, 'digital_goods' ), PHP_INT_MAX, 2 );
		\WP_Mock::expectFilterAdded( 'woocommerce_product_get_tax_status', array( $user, 'digital_goods_verify' ), PHP_INT_MAX, 2 );

		\WP_Mock::expectActionAdded( 'wp_enqueue_scripts', array( $user, 'scripts' ) );
		\WP_Mock::expectActionAdded( 'woocommerce_checkout_update_order_review', array( $user, 'b2b_taxes' ), PHP_INT_MAX );
		\WP_Mock::expectActionAdded( 'woocommerce_after_checkout_validation', array( $user, 'checkout_validation' ), PHP_INT_MAX, 2 );
		\WP_Mock::expectActionAdded( 'woocommerce_checkout_update_order_meta', array( $user, 'update_order_meta' ) );

		$user->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers ::__construct
	 * @covers ::scripts
	 */
	public function testScripts() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'admin_url',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_create_nonce',
			array(
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_localize_script',
			array(
				'return' => true,
			)
		);

		$user->scripts();
	}

	/**
	 * @covers ::__construct
	 * @covers ::checkout_fields
	 */
	public function testCheckoutFieldsB2bNone() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return' => 'none',
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'return' => 'none',
			)
		);

		$this->assertEquals(
			array(),
			$user->checkout_fields( array() )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::checkout_fields
	 */
	public function testCheckoutFieldsB2bNotNone() {
		$user = new UserView();

		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return' => 'not_none',
			)
		);
		\WP_Mock::userFunction(
			'get_option',
			array(
				'return' => 'not_none',
			)
		);

		$this->assertEquals(
			array(
				'business_check'  => array(
					'label'    => 'Are you making this purchase as a Business entity?',
					'type'     => 'checkbox',
					'required' => false,
					'class'    => array( 'wc-euvat-business-check', 'update_totals_on_change' ),
					'clear'    => true,
					'priority' => 5,
				),
				'business_tax_id' => array(
					'label'    => 'Business Tax ID',
					'type'     => 'text',
					'required' => false,
					'class'    => array( 'form-row-wide', 'wc-euvat-hidden', 'update_totals_on_change' ),
					'priority' => 6,
				),
			),
			$user->checkout_fields( array() )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::digital_goods
	 */
	public function testDigitalGoodsNoTax() {
		$user                     = new UserView();
		$user->enable_digital_tax = 'no';

		$this->assertEquals(
			'tax-class',
			$user->digital_goods( 'tax-class', (object) array() )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::digital_goods
	 */
	public function testDigitalGoodsWithTax() {
		$user                     = new UserView();
		$user->enable_digital_tax = 'yes';

		$product = new class() {
			function get_virtual() {
				return false;
			}

			function get_downloadable() {
				return false;
			}
		};

		$this->assertEquals(
			'tax-class',
			$user->digital_goods( 'tax-class', $product )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::digital_goods
	 */
	public function testDigitalGoodsWithDigitalTax() {
		$user                     = new UserView();
		$user->enable_digital_tax = 'yes';

		$product = new class() {
			function get_virtual() {
				return true;
			}

			function get_downloadable() {
				return true;
			}
		};

		$this->assertEquals(
			'digital-goods',
			$user->digital_goods( 'tax-class', $product )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::digital_goods_verify
	 */
	public function testDigitalGoodsVerifyNoTax() {
		$user                     = new UserView();
		$user->enable_digital_tax = 'no';

		$this->assertEquals(
			'tax-status',
			$user->digital_goods_verify( 'tax-status', (object) array() )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::digital_goods_verify
	 */
	public function testDigitalGoodsVerifyWithTax() {
		$user                     = new UserView();
		$user->enable_digital_tax = 'yes';

		$product = new class() {
			function get_virtual() {
				return false;
			}

			function get_downloadable() {
				return false;
			}
		};

		$this->assertEquals(
			'tax-status',
			$user->digital_goods_verify( 'tax-status', $product )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::digital_goods_verify
	 */
	public function testDigitalGoodsVerifyWithTaxNoRates() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\UserView' )
						->shouldAllowMockingProtectedMethods()
						->makePartial();
		$mock->shouldReceive( 'get_digital_tax_rate_for_user' )->andReturn( array() );
		$mock->enable_digital_tax = 'yes';

		$product = new class() {
			function get_virtual() {
				return true;
			}

			function get_downloadable() {
				return true;
			}
		};

		$this->assertEquals(
			'none',
			$mock->digital_goods_verify( 'tax-status', $product )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::digital_goods_verify
	 */
	public function testDigitalGoodsVerifyWithTaxWithRates() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\UserView' )
						->shouldAllowMockingProtectedMethods()
						->makePartial();
		$mock->shouldReceive( 'get_digital_tax_rate_for_user' )->andReturn( array( 'standard_rate' => '10.00' ) );
		$mock->enable_digital_tax = 'yes';

		$product = new class() {
			function get_virtual() {
				return true;
			}

			function get_downloadable() {
				return true;
			}
		};

		$this->assertEquals(
			'tax-status',
			$mock->digital_goods_verify( 'tax-status', $product )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::b2b_taxes
	 */
	public function testB2bTaxesNoStatus() {
		$user                   = new UserView();
		$user->b2b_sales_status = 'none';

		$this->assertEmpty( $user->b2b_taxes( 'checkout_data' ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::b2b_taxes
	 */
	public function testB2bTaxesWithStatusNoBusinessCheck() {
		$user                   = new UserView();
		$user->b2b_sales_status = 'not_none';

		$this->assertEmpty( $user->b2b_taxes( 'checkout_data=none' ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::b2b_taxes
	 */
	public function testB2bTaxesWithStatusEmptyBusinessCheck() {
		$user                   = new UserView();
		$user->b2b_sales_status = 'not_none';

		$this->assertEmpty( $user->b2b_taxes( 'business_check=' ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::b2b_taxes
	 * @covers ::validate_b2b_sales_home_country
	 */
	public function testB2bTaxesWithStatusWithBusinessCheck() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\UserView' )->makePartial();
		$mock->shouldReceive( 'validate_b2b_sales_home_country' )->andReturn();
		$mock->b2b_sales_status = 'not_none';
		$mock->store_country    = 'france';

		$this->assertEmpty( $mock->b2b_taxes( 'business_check=yes&billing_country=france' ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::b2b_taxes
	 * @covers ::validate_b2b_sales_outside_home_country
	 */
	public function testB2bTaxesWithStatusWithBusinessCheckOtherCountry() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\UserView' )->makePartial();
		$mock->shouldReceive( 'validate_b2b_sales_outside_home_country' )->andReturn();
		$mock->b2b_sales_status = 'not_none';
		$mock->store_country    = 'slovenia';

		$this->assertEmpty( $mock->b2b_taxes( 'business_check=yes&billing_country=france' ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::validate_b2b_sales_home_country
	 */
	public function testValidateB2bSales() {
		$user                              = new UserView();
		$user->b2b_charge_tax_home_country = 'no';

		\WP_Mock::expectFilterAdded( 'woocommerce_product_get_tax_class', array( $user, 'zero_rate_tax' ), PHP_INT_MAX, 2 );
		\WP_Mock::expectFilterAdded( 'woocommerce_product_get_tax_status', array( $user, 'zero_rate_tax_verify' ), PHP_INT_MAX, 2 );

		$user->validate_b2b_sales_home_country( array() );
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers ::__construct
	 * @covers ::validate_b2b_sales_home_country
	 */
	public function testValidateB2bSalesNoVies() {
		$user                              = new UserView();
		$user->b2b_charge_tax_home_country = 'yes';
		$user->b2b_no_tax_valid_vies       = 'no';

		$this->assertEmpty( $user->validate_b2b_sales_home_country( array() ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::validate_b2b_sales_home_country
	 */
	public function testValidateB2bSalesEmptyTaxId() {
		$user                              = new UserView();
		$user->b2b_charge_tax_home_country = 'yes';
		$user->b2b_no_tax_valid_vies       = 'yes';

		$this->assertEmpty( $user->validate_b2b_sales_home_country( array() ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::validate_b2b_sales_home_country
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValidCountryCode
	 */
	public function testValidateB2bSalesValidEntry() {
		$mock                              = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\UserView' )->makePartial();
		$mock->b2b_charge_tax_home_country = 'yes';
		$mock->b2b_no_tax_valid_vies       = 'yes';

		$vies = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Vies' );
		$vies->shouldReceive( 'isValid' )->andReturn( true );
		$mock->shouldReceive( 'vies' )->andReturn( $vies );

		$mock->validate_b2b_sales_home_country( array( 'business_tax_id' => 'not_empty' ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::validate_b2b_sales_home_country
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::isValidCountryCode
	 */
	public function testValidateB2bSalesInvalidEntry() {
		$mock                              = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\UserView' )->makePartial();
		$mock->b2b_charge_tax_home_country = 'yes';
		$mock->b2b_no_tax_valid_vies       = 'yes';

		$vies = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Vies' );
		$vies->shouldReceive( 'isValid' )->andReturn( false );
		$mock->shouldReceive( 'vies' )->andReturn( $vies );

		$this->assertEmpty( $mock->validate_b2b_sales_home_country( array( 'business_tax_id' => 'not_empty' ) ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::validate_b2b_sales_outside_home_country
	 */
	public function testValidateB2bSalesOutsideHomeCountryNoTax() {
		$user                             = new UserView();
		$user->b2b_no_tax_outside_country = 'no';

		$this->assertEmpty( $user->validate_b2b_sales_outside_home_country() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::validate_b2b_sales_outside_home_country
	 */
	public function testValidateB2bSalesOutsideHomeCountryWithTax() {
		$user                             = new UserView();
		$user->b2b_no_tax_outside_country = 'yes';

		\WP_Mock::expectFilterAdded( 'woocommerce_product_get_tax_class', array( $user, 'zero_rate_tax' ), PHP_INT_MAX, 2 );
		\WP_Mock::expectFilterAdded( 'woocommerce_product_get_tax_status', array( $user, 'zero_rate_tax_verify' ), PHP_INT_MAX, 2 );

		$user->validate_b2b_sales_outside_home_country();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers ::__construct
	 * @covers ::zero_rate_tax
	 */
	public function testZeroTaxRate() {
		$user = new UserView();
		$this->assertEquals(
			'Zero rate',
			$user->zero_rate_tax( 'tax-class', (object) array() )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::zero_rate_tax_verify
	 */
	public function testZeroTaxRateVerifyZeroRate() {
		$user    = new UserView();
		$product = new class() {
			function get_tax_class() {
				return 'Zero rate';
			}
		};

		$this->assertEquals(
			'none',
			$user->zero_rate_tax_verify( 'tax-status', $product )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::zero_rate_tax_verify
	 */
	public function testZeroTaxRateVerifyNotZeroRate() {
		$user    = new UserView();
		$product = new class() {
			function get_tax_class() {
				return 'Not Zero';
			}
		};

		$this->assertEquals(
			'tax-status',
			$user->zero_rate_tax_verify( 'tax-status', $product )
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::checkout_validation
	 */
	public function testCheckoutValidationNoTaxRequired() {
		$user                      = new UserView();
		$user->b2b_tax_id_required = 'no';
		$errors                    = \Mockery::mock( '\WP_Error' );

		$this->assertEmpty( $user->checkout_validation( array(), $errors ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::checkout_validation
	 */
	public function testCheckoutValidationNoBusinessCheck() {
		$user                      = new UserView();
		$user->b2b_tax_id_required = 'yes';
		$errors                    = \Mockery::mock( '\WP_Error' );

		$this->assertEmpty( $user->checkout_validation( array(), $errors ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::checkout_validation
	 */
	public function testCheckoutValidationTaxIdNotEmpty() {
		$user                      = new UserView();
		$user->b2b_tax_id_required = 'yes';

		$errors = \Mockery::mock( '\WP_Error' );
		$this->assertEmpty(
			$user->checkout_validation(
				array(
					'business_check'  => 'yes',
					'business_tax_id' => 'not_empty',
				),
				$errors
			)
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::checkout_validation
	 */
	public function testCheckoutValidationTaxIdEmpty() {
		$user                      = new UserView();
		$user->b2b_tax_id_required = 'yes';

		$errors = \Mockery::mock( '\WP_Error' );
		$errors->shouldReceive( 'add' )->andReturn();

		$this->assertEmpty(
			$user->checkout_validation(
				array(
					'business_check'  => 'yes',
					'business_tax_id' => '',
				),
				$errors
			)
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::update_order_meta
	 */
	public function testUpdateOrderMetaNoBusinessCheck() {
		$user = new UserView();
		$this->assertEmpty( $user->update_order_meta( 20 ) );
	}

	/**
	 * @covers ::__construct
	 * @covers ::update_order_meta
	 */
	public function testUpdateOrderMetaWithBusinessCheck() {
		$user = new UserView();

		$_POST['business_check']  = 'yes';
		$_POST['business_tax_id'] = 'TAX_ID';

		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'times'  => 2,
				'return' => 'not_empty',
			)
		);
		\WP_Mock::userFunction(
			'update_post_meta',
			array(
				'times'  => 2,
				'return' => 'true',
			)
		);

		$user->update_order_meta( 20 );
	}

	/**
	 * @covers ::__construct
	 * @covers ::vies
	 * @covers \Niteo\WooCart\EUVatTaxes\Vies::__construct
	 */
	public function testVies() {
		$user = new UserView();
		$this->assertInstanceOf( '\Niteo\WooCart\EUVatTaxes\Vies', $user->vies() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::get_digital_tax_rate_for_user
	 */
	public function testGetDigitalTaxRate() {
		$user = new UserView();
		$wc   = \Mockery::mock( 'alias:\WC_Tax' );
		$wc->shouldReceive( 'get_rates' )->andReturn( array() );

		$this->assertEquals( array(), $user->get_digital_tax_rate_for_user() );
	}

}
