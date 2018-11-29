<?php

namespace Niteo\WooCart\BetterTaxHandling\Vies {

	/**
	 * Class that serves response to the client.
	 *
	 * @since 1.0.0
	 */
	class Response {

		/**
		 * @var string
		 */
		private $countryCode;

		/**
		 * @var string
		 */
		private $vatNumber;

		/**
		 * @var string
		 */
		private $requestDate;

		/**
		 * @var boolean
		 */
		private $valid;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var string
		 */
		private $address;

		/**
		 * Return country code.
		 *
		 * @return string
		 */
		public function getCountryCode() {
			return $this->countryCode;
		}

		/**
		 * Return VAT number.
		 *
		 * @return string
		 */
		public function getVatNumber() {
			return $this->vatNumber;
		}

		/**
		 * Return Date & Time.
		 *
		 * @return string
		 */
		public function getRequestDate() {
			if ( ! $this->requestDate instanceof \DateTime ) {
				$this->requestDate = new \DateTime( $this->requestDate );
			}

			return $this->requestDate;
		}

		/**
		 * Return VAT ID status.
		 *
		 * @return boolean
		 */
		public function isValid() {
			return $this->valid;
		}

		/**
		 * Return name.
		 *
		 * @return string
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * Return address.
		 *
		 * @return string
		 */
		public function getAddress() {
			return $this->address;
		}

	}

}
