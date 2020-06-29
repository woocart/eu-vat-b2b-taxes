<?php
/**
 * Tests the rates class.
 *
 * @package eu-vat-b2b-taxes
 */

use Niteo\WooCart\EUVatTaxes\Rates;
use PHPUnit\Framework\TestCase;

class RatesTest extends TestCase {

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
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 */
	public function testConstructor() {
		$rates = new Rates();

		\WP_Mock::expectActionAdded( 'admin_init', array( $rates, 'init' ) );

		$rates->__construct();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::init
	 */
	public function testInitNoAdminPage() {
		global $pagenow;

		$pagenow = 'post.php';
		$rates   = new Rates();

		$this->assertNull(
			$rates->init()
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::init
	 */
	public function testInitNoPage() {
		global $pagenow;

		$pagenow = 'admin.php';
		$rates   = new Rates();

		$this->assertNull(
			$rates->init()
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::init
	 */
	public function testInitNoTab() {
		global $pagenow;

		$pagenow          = 'admin.php';
		$_REQUEST['page'] = 'wc-settings';
		$rates            = new Rates();

		$this->assertNull(
			$rates->init()
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::init
	 */
	public function testInitNoSection() {
		global $pagenow;

		$rates = new Rates();

		$pagenow          = 'admin.php';
		$_REQUEST['tab']  = 'tax';
		$_REQUEST['page'] = 'wc-settings';

		\WP_Mock::expectActionAdded( 'admin_footer', array( $rates, 'footer' ) );
		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return' => true,
			)
		);

		$rates->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::init
	 */
	public function testInit() {
		global $pagenow;

		$rates = new Rates();

		$pagenow             = 'admin.php';
		$_REQUEST['tab']     = 'tax';
		$_REQUEST['page']    = 'wc-settings';
		$_REQUEST['section'] = 'reduced-rate';

		\WP_Mock::expectActionAdded( 'admin_footer', array( $rates, 'footer' ) );
		\WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return' => true,
			)
		);

		$rates->init();
		\WP_Mock::assertHooksAdded();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::footer
	 */
	public function testFooter() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'get_tax_rates' )
				 ->andReturn( true );

		$mock->known_rates = array(
			'standard_rate' => esc_html__( 'Standard Rate', 'eu-vat-b2b-taxes' ),
			'reduced_rate'  => esc_html__( 'Reduced Rate', 'eu-vat-b2b-taxes' ),
		);

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			array(
				'times'  => 1,
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'times'  => 1,
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'wp_localize_script',
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

		$mock->footer();
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 */
	public function testGetTaxRatesPresent() {
		$rates        = new Rates();
		$rates->rates = array(
			'DE' => array(
				'standard_rate' => '20.00',
			),
			'SI' => array(
				'standard_rate' => '18.00',
			),
		);

		$this->assertEquals(
			array(
				'DE' => array(
					'standard_rate' => '20.00',
				),
				'SI' => array(
					'standard_rate' => '18.00',
				),
			),
			$rates->get_tax_rates()
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_iso_code
	 */
	public function testGetTaxRatesNotPresent() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'fetch_tax_rates' )
				->andReturn(
					array(
						'DE' => array(
							'standard_rate' => '20.00',
						),
						'SI' => array(
							'standard_rate' => '18.00',
						),
					)
				);
		\WP_Mock::userFunction(
			'get_site_transient',
			array(
				'return' => false,
			)
		);
		\WP_Mock::userFunction(
			'set_site_transient',
			array(
				'return' => true,
			)
		);

		$this->assertEquals(
			array(
				'DE' => array(
					'standard_rate' => '20.00',
				),
				'SI' => array(
					'standard_rate' => '18.00',
				),
			),
			$mock->get_tax_rates()
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_iso_code
	 */
	public function testGetTaxRatesFranceMonaco() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'fetch_tax_rates' )
				->andReturn(
					array(
						'GB' => array(
							'standard_rate' => '20.00',
						),
						'FR' => array(
							'standard_rate' => '18.00',
						),
					)
				);
		\WP_Mock::userFunction(
			'get_site_transient',
			array(
				'return' => false,
			)
		);
		\WP_Mock::userFunction(
			'set_site_transient',
			array(
				'return' => true,
			)
		);

		$this->assertEquals(
			array(
				'GB' => array(
					'standard_rate' => '20.00',
				),
				'FR' => array(
					'standard_rate' => '18.00',
				),
				'MC' => array(
					'standard_rate' => '18.00',
				),
				'IM' => array(
					'standard_rate' => '20.00',
					'country'       => 'Isle of Man',
				),
			),
			$mock->get_tax_rates()
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_code
	 */
	public function testGetTaxCode() {
		$rates = new Rates();

		$this->assertEquals( 'IN', $rates->get_tax_code( 'IN' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_code
	 */
	public function testGetTaxCodeExceptionGR() {
		$rates = new Rates();

		$this->assertEquals( 'EL', $rates->get_tax_code( 'GR' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_code
	 */
	public function testGetTaxCodeExceptionGB() {
		$rates = new Rates();

		$this->assertEquals( 'UK', $rates->get_tax_code( 'GB' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_code
	 */
	public function testGetTaxCodeExceptionIM() {
		$rates = new Rates();

		$this->assertEquals( 'UK', $rates->get_tax_code( 'IM' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_code
	 */
	public function testGetTaxCodeExceptionMC() {
		$rates = new Rates();

		$this->assertEquals( 'FR', $rates->get_tax_code( 'MC' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_iso_code
	 */
	public function testGetIsoCode() {
		$rates = new Rates();

		$this->assertEquals( 'IN', $rates->get_iso_code( 'IN' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_iso_code
	 */
	public function testGetIsoCodeExceptionUK() {
		$rates = new Rates();

		$this->assertEquals( 'GB', $rates->get_iso_code( 'UK' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_iso_code
	 */
	public function testGetIsoCodeExceptionEL() {
		$rates = new Rates();

		$this->assertEquals( 'GR', $rates->get_iso_code( 'EL' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rate_for_country
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 */
	public function testGetTaxRatesForCountryFalse() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'get_tax_rates' )
				 ->andReturn( array() );

		$this->assertFalse( $mock->get_tax_rate_for_country( 'ABC' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rate_for_country
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 */
	public function testGetTaxRatesForCountryNotArray() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'get_tax_rates' )
				 ->andReturn( 'String' );

		$this->assertFalse( $mock->get_tax_rate_for_country( 'ABC' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rate_for_country
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 */
	public function testGetTaxRatesForCountryNoCountryCode() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'get_tax_rates' )
				->andReturn(
					array(
						'SI' => array(
							'standard_rate' => '18.00',
						),
					)
				);

		$this->assertFalse( $mock->get_tax_rate_for_country( 'ABC' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rate_for_country
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 */
	public function testGetTaxRatesForCountryNoStandardRate() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'get_tax_rates' )
				->andReturn(
					array(
						'SI' => array(
							'reduced_rate' => '18.00',
						),
					)
				);

		$this->assertFalse( $mock->get_tax_rate_for_country( 'SI' ) );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rate_for_country
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 */
	public function testGetTaxRatesForCountryStandardRate() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'get_tax_rates' )
				->andReturn(
					array(
						'SI' => array(
							'standard_rate' => '18.00',
						),
					)
				);

		$this->assertEquals(
			'18.00',
			$mock->get_tax_rate_for_country( 'SI' )
		);
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_file_path
	 */
	public function testFetchTaxRates() {
		$rates = new Rates();
		$this->assertFalse( $rates->fetch_tax_rates() );
	}

	/**
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::__construct
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::fetch_tax_rates
	 * @covers \Niteo\WooCart\EUVatTaxes\Rates::get_file_path
	 */
	public function testFetchTaxRatesDiffFile() {
		$mock = \Mockery::mock( '\Niteo\WooCart\EUVatTaxes\Rates' )->makePartial();
		$mock->shouldReceive( 'get_file_path' )
				 ->andReturn( './tests/fixtures/rates.json' );
		$this->assertEquals(
			array(
				'AT' => array(
					'country'            => 'Austria',
					'standard_rate'      => 20,
					'reduced_rate'       => 10,
					'reduced_rate_alt'   => 13,
					'super_reduced_rate' => false,
					'parking_rate'       => 12,
				),
				'BE' => array(
					'country'            => 'Belgium',
					'standard_rate'      => 21,
					'reduced_rate'       => 12,
					'reduced_rate_alt'   => 6,
					'super_reduced_rate' => false,
					'parking_rate'       => 12,
				),
			),
			$mock->fetch_tax_rates()
		);
	}

}
