<?php
/**
 * Tests the vies class.
 *
 * @package advanced-taxes-woocommerce
 */

use Niteo\WooCart\AdvancedTaxes\Vies;
use Niteo\WooCart\AdvancedTaxes\Vies\Client;
use Niteo\WooCart\AdvancedTaxes\Vies\Response;
use PHPUnit\Framework\TestCase;

class ViesTest extends TestCase {

	private $validator;

	function setUp() {
		\WP_Mock::setUsePatchwork( true );
		\WP_Mock::setUp();

		$this->validator = new Vies();
	}

	function tearDown() {
		$this->addToAssertionCount(
			\Mockery::getContainer()->mockery_getExpectationCount()
		);

		\WP_Mock::tearDown();
	}

	/**
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::__construct
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::isValidCountryCode
	 * @dataProvider getValidVatIds
	 */
	public function testValid( $value ) {
		$this->assertTrue( $this->validator->isValid( $value ) );
	}

	/**
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::__construct
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::isValidCountryCode
	 * @dataProvider getInvalidVatIds
	 */
	public function testInvalid( $value ) {
		$this->assertFalse( $this->validator->isValid( $value ) );
	}

	/**
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::__construct
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::getViesClient
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::isValidCountryCode
	 */
	public function testValidWithVies() {
		$client = $this->getClientMock();
		$client->expects( $this->once() )
		   ->method( 'checkVat' )
		   ->with( 'NL', '002065538B01' )
		   ->willReturn( $this->getResponseMock( true ) );

		$this->validator = new Vies( $client );
		$this->assertTrue( $this->validator->isValid( 'NL002065538B01', true ) );
	}

	/**
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::__construct
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::isValid
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::getViesClient
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies::isValidCountryCode
	 */
	public function testInvalidWithVies() {
		$client = $this->getClientMock();
		$client->expects( $this->once() )
		   ->method( 'checkVat' )
		   ->with( 'NL', '123456789B01' )
		   ->willReturn( $this->getResponseMock( false ) );

		$this->validator = new Vies( $client );
		$this->assertFalse( $this->validator->isValid( 'NL123456789B01', true ) );
	}

	/**
	 * @return array
	 */
	public function getValidVatIds() {
		return [
			// Examples from Wikipedia (https://en.wikipedia.org/wiki/VAT_identification_number)
			[ 'ATU99999999' ],       // Austria
			[ 'BE0999999999' ],      // Belgium
			[ 'BE1999999999' ],      // Belgium
			[ 'HR12345678901' ],     // Croatia
			[ 'CY99999999L' ],       // Cyprus
			[ 'DK99999999' ],        // Denmark
			[ 'FI99999999' ],        // Finland
			[ 'FRXX999999999' ],     // France
			[ 'DE999999999' ],       // Germany
			[ 'HU12345678' ],        // Hungary
			[ 'IE1234567T' ],        // Ireland
			[ 'IE1234567TW' ],       // Ireland
			[ 'IE1234567FA' ],       // Ireland (since January 2013)
			[ 'NL999999999B99' ],    // The Netherlands
			[ 'NO999999999' ],       // Norway
			[ 'ES99999999R' ],       // Spain
			[ 'SE999999999901' ],    // Sweden
			[ 'GB999999973' ],       // United Kingdom (standard)
			[ 'GBGD001' ],           // United Kingdom (government departments)
			[ 'GBHA599' ],           // United Kingdom (health authorities)

		// Examples from the EU (http://ec.europa.eu/taxation_customs/vies/faqvies.do#item_11)
			[ 'ATU99999999' ],       // AT-Austria
			[ 'BE0999999999' ],      // BE-Belgium
			[ 'BG999999999' ],       // BG-Bulgaria
			[ 'BG9999999999' ],      // BG-Bulgaria
			[ 'CY99999999L' ],       // CY-Cyprus
			[ 'CZ99999999' ],        // CZ-Czech Republic
			[ 'CZ999999999' ],       // CZ-Czech Republic
			[ 'CZ9999999999' ],      // CZ-Czech Republic
			[ 'DE999999999' ],       // DE-Germany
			[ 'DK99999999' ],        // DK-Denmark
			[ 'EE999999999' ],       // EE-Estonia
			[ 'EL999999999' ],       // EL-Greece
			[ 'ESX9999999X' ],       // ES-Spain
			[ 'FI99999999' ],        // FI-Finland
			[ 'FRXX999999999' ],     // FR-France
			[ 'GB999999999' ],       // GB-United Kingdom
			[ 'GB999999999999' ],    // GB-United Kingdom
			[ 'GBGD999' ],           // GB-United Kingdom
			[ 'GBHA999' ],           // GB-United Kingdom
			[ 'HR99999999999' ],     // HR-Croatia
			[ 'HU99999999' ],        // HU-Hungary
			[ 'IE9S99999L' ],        // IE-Ireland
			[ 'IE9999999WI' ],       // IE-Ireland
			[ 'IT99999999999' ],     // IT-Italy
			[ 'LT999999999' ],       // LT-Lithuania
			[ 'LT999999999999' ],    // LT-Lithuania
			[ 'LU99999999' ],        // LU-Luxembourg
			[ 'LV99999999999' ],     // LV-Latvia
			[ 'MT99999999' ],        // MT-Malta
			[ 'NL999999999B99' ],    // NL-The Netherlands
			[ 'PL9999999999' ],      // PL-Poland
			[ 'PT999999999' ],       // PT-Portugal
			[ 'RO999999999' ],       // RO-Romania
			[ 'SE999999999999' ],    // SE-Sweden
			[ 'SI99999999' ],        // SI-Slovenia
			[ 'SK9999999999' ],      // SK-Slovakia

		// Real world examples
			[ 'GB226148083' ],       // Fuller's Brewery, United Kingdom
			[ 'NL002230884B01' ],    // Albert Heijn BV., The Netherlands
			[ 'ESG82086810' ],       // Fundación Telefónica, Spain
			[ 'IE9514041I' ],        // Lego Systems A/S, Denmark with Irish VAT ID
			[ 'IE9990705T' ],        // Amazon EU Sarl, Luxembourg with Irish VAT ID
			[ 'DK61056416' ],        // Carlsberg A/S, Denmark
			[ 'BE0648836958' ],      // Delhaize Logistics, Belgium
			[ 'CZ00514152' ],        // Budějovický Budvar, Budweiser, Czech Republic

		// Various examples
			[ 'FR9X999999999' ],
			[ 'NL123456789B01' ],
			[ 'IE9574245O' ],
		];
	}

	/**
	 * @return array
	 */
	public function getInvalidVatIds() {
		return [
			[ null ],
			[ '' ],
			[ '123456789' ],
			[ 'XX123' ],
			[ 'GB999999973dsflksdjflsk' ],
			[ 'BE2999999999' ], // Belgium - "the first digit following the prefix is always zero ("0") or ("1")"
		];
	}

	/**
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies\Client
	 */
	private function getClientMock() {
		return $this->getMockBuilder( '\Niteo\WooCart\AdvancedTaxes\Vies\Client' )
		->disableOriginalConstructor()
		->getMock();
	}

	/**
	 * @covers \Niteo\WooCart\AdvancedTaxes\Vies\Response
	 */
	private function getResponseMock( $valid ) {
		$mock = $this->getMockBuilder( '\Niteo\WooCart\AdvancedTaxes\Vies\Response' )->getMock();

		$mock->expects( $this->any() )
		 ->method( 'isValid' )
		 ->willReturn( $valid );

		return $mock;
	}

}
