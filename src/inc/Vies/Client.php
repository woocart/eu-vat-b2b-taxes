<?php

namespace Niteo\WooCart\EUVatTaxes\Vies {

	use Exception;
	use SoapClient;
	use SoapFault;

	/**
	 * Class that serves as client for processing our request for Tax ID validation.
	 *
	 * @since 1.0.0
	 */
	class Client {

		/**
		 * @var string
		 */
		private $wsdl = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

		/**
		 * @var \SoapClient
		 */
		private $soapClient;

		/**
		 * @var array
		 */
		private $classmap = array(
			'checkVatResponse' => 'Niteo\WooCart\EUVatTaxes\Vies\Response',
		);

		/**
		 * Class constructor.
		 *
		 * @param string|null $wsdl URL to WSDL
		 */
		public function __construct( $wsdl = null ) {
			if ( $wsdl ) {
				$this->wsdl = $wsdl;
			}
		}

		/**
		 * Check VAT.
		 *
		 * @param string $countryCode Country code
		 * @param string $vatNumber VAT number
		 *
		 * @return Niteo\WooCart\EUVatTaxes\Vies\Response
		 * @throws Exception
		 */
		public function checkVat( $countryCode, $vatNumber ) {
			try {
				return $this->getSoapClient()->checkVat(
					array(
						'countryCode' => $countryCode,
						'vatNumber'   => $vatNumber,
					)
				);
			} catch ( SoapFault $e ) {
				throw new Exception( 'Error communicating with VIES service.', 0, $e );
			}
		}

		/**
		 * Get SOAP client.
		 *
		 * @return \SoapClient
		 */
		private function getSoapClient() {
			if ( null === $this->soapClient ) {
				$this->soapClient = new \SoapClient(
					$this->wsdl,
					array(
						'classmap'   => $this->classmap,
						'user_agent' => 'Mozilla/5.0 (compatible; EU VAT & B2B Taxes for WooCommerce Plugin; +https://woocart.com)',
						'exceptions' => true,
					)
				);
			}

			return $this->soapClient;
		}

	}

}
