# Better Tax Handling Plugin

The plugin adds three sections to the default Tax menu in WooCommerce: 

- Tax Handling for B2B
- EU Tax Handling - Digital Goods (B2C)
- EU Tax Handling - Distance Selling (B2C)

The defaults are checked boxes and should be the correct setting for all EU countries. That said, make sure to confirm the settings with your accountant or tax advisor.


## Plugin Settings

### Tax Handling for B2B

This section handles taxes for B2B (Business to Business). Let's have a look at the options for B2B tax handling offered by the plugin and how it affects tax calculation for the customer.

<img src="images/tax-b2b.png" alt="Tax Handling for B2B">

- **B2B Sales** - When enabled (for either EU Store or Non-EU Store), this option adds an option in the checkout form for making the purchase as a Business Entity. When the visitor clicks the B2B option in the checkout form, an additional Tax ID field opens for entering the Business Tax ID. Make sure to click Save at the bottom of the page after checking the enable box.

- **Tax ID field required for B2B** - Enabling this option makes it mandatory for all B2B customers to provide a Business Tax ID.

- **B2B sales in the home country** - Whether B2B sales in the home country are taxed or not.

- **B2B sales in the EU when VIES/VAT ID is provided** - Whether B2B sales in EU are taxed or not. This option is disabled if you chose the Non-EU Store in the first dropdown.

- **B2B sales outside the country** - Whether B2B sales outside the home country and EU are taxed or not. For example: selling from Germany to the USA.

### Digital Goods (B2C - EU)

This part of the plugin handles taxes for [Digital Goods](https://quaderno.io/resources/eu-vat-guide/) for consumers when selling to the EU. 

<img src="images/tax-b2c-digital.png" alt="Digital Goods (B2C)">

- **EU Tax Handling for Digital Goods** - If you plan on selling Digital Goods in or into the EU, enable this option. Make sure to click Save at the bottom of the page. It's advised you use the next option ot import tax rates for Digital Goods.

- **Import tax rates for all EU countries and create tax class Digital Goods** - Clicking this option imports tax rates for all EU countries (standard VAT rate) and creates a tax class Digital Goods. You must choose this tax class to all the digital goods in your store to make the proper tax calculations. Digital Goods are charged the customer's country standard VAT rate. 

### Distance Selling (B2C - EU)

This part of the plugin handles taxes for Digital Selling for Consumers. As a store owner, you will need to register for EU VAT ID in countries where you reach [Distance Selling EU Tax thresholds](https://www.vatlive.com/eu-vat-rules/distance-selling/distance-selling-eu-vat-thresholds/).

<img src="images/tax-b2c-distance.png" alt="Distance Selling (B2C)">

For this to work properly, you need to go through three steps.

- **Enable EU VAT Handling for Distance Selling** - Enabling this option will allow you to collect taxes on transactions done via countries which fall under the "Distance Selling" category. You will be responsible for specifying the countries for this category in the next option. Only the countries added will be applicable for tax calculation at the time of checkout. Also, this option has no impact on the B2B transactions.

- **Select countries for which you would like to import tax rates** - Add countries to this multi-select option box whose tax rates you would like to import.

- **Import tax rates for specific EU countries** - Clicking this option imports tax rates for all EU countries added to the above option. Any country added outside of EU will be ignored by this option and only taxes for countries which fall within EU will be imported.

## Reports
